<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Scheme;
use App\Services\EligibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EligibilityController extends Controller
{
    public function __construct(private EligibilityService $eligibilityService) {}

    /**
     * POST /api/v1/eligibility/check
     *
     * Body:
     * {
     *   "scheme_id": "...",
     *   "applicant_data": {
     *     "age": 22,
     *     "gender": "female",
     *     "state": "UP",
     *     "annual_income": 80000,
     *     "caste_category": "sc"
     *   }
     * }
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'scheme_id'      => ['required', 'string'],
            'applicant_data' => ['required', 'array'],
        ]);

        $scheme = Scheme::findOrFail($request->scheme_id);

        $result = $this->eligibilityService->evaluate($scheme, $request->applicant_data);

        return response()->json([
            'success' => true,
            'data'    => array_merge($result, [
                'scheme_id'    => (string) $scheme->_id,
                'scheme_title' => $scheme->title,
            ]),
        ]);
    }

    /**
     * POST /api/v1/eligibility/bulk-check
     *
     * Check eligibility across multiple schemes at once (for dashboard overview).
     * Body: { "scheme_ids": [...], "applicant_data": {...} }
     */
    public function bulkCheck(Request $request): JsonResponse
    {
        $request->validate([
            'scheme_ids'     => ['required', 'array', 'max:20'],
            'scheme_ids.*'   => ['string'],
            'applicant_data' => ['required', 'array'],
        ]);

        $schemes = Scheme::whereIn('_id', $request->scheme_ids)->get();
        $results = [];

        foreach ($schemes as $scheme) {
            $result    = $this->eligibilityService->evaluate($scheme, $request->applicant_data);
            $results[] = array_merge($result, [
                'scheme_id'    => (string) $scheme->_id,
                'scheme_title' => $scheme->title,
                'category_id'  => $scheme->category_id,
            ]);
        }

        // Sort: eligible first
        usort($results, fn ($a, $b) => $b['eligible'] <=> $a['eligible']);

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }
}
