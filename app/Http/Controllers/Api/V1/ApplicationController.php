<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ApplicationStatusUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessBlockchainLogJob;
use App\Models\Application;
use App\Models\Scheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * POST /api/v1/applications/submit
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'scheme_id'       => ['required', 'string'],
            'conversation_id' => ['nullable', 'string'],
            'interview_data'  => ['required', 'array'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        // Ensure scheme exists and is active
        $scheme = Scheme::where('_id', $request->scheme_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Prevent duplicate active applications
        $existing = Application::where('user_id', (string) $request->user()->_id)
            ->where('scheme_id', $request->scheme_id)
            ->whereIn('status', [
                Application::STATUS_SUBMITTED,
                Application::STATUS_UNDER_REVIEW,
            ])->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active application for this scheme.',
                'data'    => ['application_id' => (string) $existing->_id],
            ], 409);
        }

        $application = Application::create([
            'user_id'         => (string) $request->user()->_id,
            'scheme_id'       => (string) $scheme->_id,
            'conversation_id' => $request->conversation_id,
            'status'          => Application::STATUS_SUBMITTED,
            'interview_data'  => $request->interview_data,
            'notes'           => $request->notes,
            'sla_deadline'    => now()->addDays(30), // 30-day SLA
        ]);

        // Dispatch async blockchain logging job
        ProcessBlockchainLogJob::dispatch($application)->onQueue('blockchain');

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data'    => [
                'application_id' => (string) $application->_id,
                'scheme_title'   => $scheme->title,
                'status'         => $application->status,
                'sla_deadline'   => $application->sla_deadline,
            ],
        ], 201);
    }

    /**
     * GET /api/v1/applications
     * List authenticated user's own applications.
     */
    public function index(Request $request): JsonResponse
    {
        $applications = Application::where('user_id', (string) $request->user()->_id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $applications->items(),
            'meta'    => [
                'current_page' => $applications->currentPage(),
                'per_page'     => $applications->perPage(),
                'total'        => $applications->total(),
                'last_page'    => $applications->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/applications/{id}
     * Single application details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $application = Application::where('_id', $id)
            ->where('user_id', (string) $request->user()->_id)
            ->firstOrFail();

        $scheme = Scheme::find($application->scheme_id);

        return response()->json([
            'success' => true,
            'data'    => array_merge($application->toArray(), [
                'scheme' => $scheme ? [
                    'id'    => (string) $scheme->_id,
                    'title' => $scheme->title,
                ] : null,
            ]),
        ]);
    }

    /**
     * PATCH /api/v1/applications/{id}/status  (Admin only)
     * Update application status and broadcast event.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:under_review,approved,rejected'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($id);
        $application->update([
            'status' => $request->status,
            'notes'  => $request->input('notes', $application->notes),
        ]);

        // Broadcast real-time update to the citizen
        broadcast(new ApplicationStatusUpdated($application))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Application status updated.',
            'data'    => $application->fresh(),
        ]);
    }
}
