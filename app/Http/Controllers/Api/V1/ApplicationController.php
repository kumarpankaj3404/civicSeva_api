<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
            'scheme_id'       => ['required', 'integer'],
            'conversation_id' => ['nullable', 'integer'],
            'interview_data'  => ['required', 'array'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $scheme = Scheme::where('id', $request->scheme_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Prevent duplicate active applications
        $existing = Application::where('user_id', $request->user()->id)
            ->where('scheme_id', $scheme->id)
            ->whereIn('status', [
                Application::STATUS_SUBMITTED,
                Application::STATUS_UNDER_REVIEW,
            ])->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active application for this scheme.',
                'data'    => ['application_id' => $existing->id],
            ], 409);
        }

        $application = Application::create([
            'user_id'         => $request->user()->id,
            'scheme_id'       => $scheme->id,
            'conversation_id' => $request->conversation_id,
            'status'          => Application::STATUS_SUBMITTED,
            'interview_data'  => $request->interview_data,
            'notes'           => $request->notes,
            'submitted_at'    => now(),
            'sla_deadline'    => now()->addDays(30),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data'    => [
                'application_id' => $application->id,
                'scheme_title'   => $scheme->title,
                'status'         => $application->status,
                'sla_deadline'   => $application->sla_deadline,
            ],
        ], 201);
    }

    /**
     * GET /api/v1/applications
     */
    public function index(Request $request): JsonResponse
    {
        $applications = Application::where('user_id', $request->user()->id)
            ->with('scheme:id,title,ministry')
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
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $application = Application::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('scheme:id,title,ministry')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $application,
        ]);
    }

    /**
     * PATCH /api/v1/applications/{id}/status  (Admin only)
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status'  => ['required', 'in:under_review,approved,rejected'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($id);
        $application->update([
            'status'  => $request->status,
            'remarks' => $request->input('remarks', $application->remarks),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated.',
            'data'    => $application->fresh(),
        ]);
    }
}
