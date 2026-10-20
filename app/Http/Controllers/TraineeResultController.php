<?php

namespace App\Http\Controllers;

use App\Models\TraineeResult;
use App\Services\HazardEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * TraineeResultController
 *
 * Central supervisor dashboard and VR API controller.
 * Handles:
 * 1. REST API endpoints communicating with Unity VR Headset / XR Simulator.
 * 2. Multi-page web dashboard views (Overview, VR Monitor, Directory, Analytics, Guide, Profile).
 * 3. Real-time active trainee caching and telemetry sync.
 */
class TraineeResultController extends Controller
{
    /**
     * @param HazardEvaluationService $evaluationService
     */
    public function __construct(
        protected HazardEvaluationService $evaluationService
    ) {}

    // =========================================================================
    // SECTION 1: VR REST API ENDPOINTS (Unity / XR Interaction Bridge)
    // =========================================================================

    /**
     * Ingest and evaluate session telemetry uploaded by the VR headset.
     *
     * POST /api/trainee-results
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Tolerate JSON body, form data, or raw stream
        $payload = $request->json()->all() ?: json_decode($request->getContent(), true) ?: $request->all();
        if (!empty($payload)) {
            $request->merge($payload);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'score' => 'nullable|numeric',
            'hazards_found' => 'required|integer|min:0|max:13',
            'hazards_missed' => 'required|integer|min:0|max:13',
            'wrong_clicks' => 'nullable|integer|min:0',
            'completion_time' => 'required|numeric|min:0',
            'found_hazards' => 'nullable',
            'missed_hazards' => 'nullable',
        ]);

        $hazardsFound = (int) $validated['hazards_found'];
        $hazardsMissed = (int) $validated['hazards_missed'];
        $wrongClicks = (int) ($request->input('wrong_clicks', 0));
        $completionTime = (float) $validated['completion_time'];

        $totalHazards = 13;

        // Base Detection Score Percentage (0.0 - 100.0%):
        $baseScore = ($hazardsFound / $totalHazards) * 100;

        // Wrong-click penalty: 5.0% deduction per misclicked safe object / distractor
        $penalty = $wrongClicks * 5.0;

        // Final percentage score (clamped between 0.0% and 100.0%)
        $finalScore = max(0.0, min(100.0, round($baseScore - $penalty, 1)));

        // Performance Rating ('Locked In', 'Great', 'Valid Effort', 'Needs Review')
        $rating = match (true) {
            $finalScore >= 90.0 => 'Locked In',
            $finalScore >= 80.0 => 'Great',
            $finalScore >= 60.0 => 'Valid Effort',
            default             => 'Needs Review',
        };

        // Certificate Qualification Logic (Score >= 80.0%)
        $isCertified = ($finalScore >= 80.0);

        // Normalize array/string formats for hazard lists
        $foundHazards = $request->input('found_hazards');
        if (is_string($foundHazards)) {
            $foundHazards = json_decode($foundHazards, true) ?? explode(',', $foundHazards);
        }

        $missedHazards = $request->input('missed_hazards');
        if (is_string($missedHazards)) {
            $missedHazards = json_decode($missedHazards, true) ?? explode(',', $missedHazards);
        }

        $record = TraineeResult::create([
            'username' => $validated['username'],
            'score' => $finalScore,
            'hazards_found' => $hazardsFound,
            'hazards_missed' => $hazardsMissed,
            'wrong_clicks' => $wrongClicks,
            'found_hazards' => $foundHazards,
            'missed_hazards' => $missedHazards,
            'completion_time' => $completionTime,
            'performance_rating' => $rating,
            'is_certified' => $isCertified,
        ]);

        // Keep active trainee telemetry synced in cache for live VR monitor
        Cache::put('active_vr_trainee', $record->toArray(), now()->addHours(2));

        return response()->json([
            'status' => 'success',
            'message' => 'Trainee session evaluated and recorded successfully',
            'data' => $record,
        ], 201);
    }

    /**
     * Poll live active trainee status for real-time dashboard telemetry.
     *
     * GET /api/live-monitor/status or GET /api/active-trainee
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function liveMonitorStatus(Request $request): JsonResponse
    {
        $username = $request->query('username');
        if ($username && $username !== 'none') {
            $record = TraineeResult::where('username', $username)->latest()->first();
            return response()->json([
                'status' => 'success',
                'has_active' => (bool) $record,
                'record' => $record,
                'timestamp' => now()->timestamp,
            ]);
        }

        $activeTrainee = Cache::get('active_vr_trainee');
        if (!$activeTrainee) {
            $latest = TraineeResult::latest()->first();
            if ($latest) {
                $activeTrainee = [
                    'username' => $latest->username,
                    'hazards_found' => (int) $latest->hazards_found,
                    'wrong_clicks' => (int) $latest->wrong_clicks,
                    'score' => (float) $latest->score,
                    'completion_time' => (float) $latest->completion_time,
                    'found_hazards' => $latest->found_hazards,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'has_active' => !empty($activeTrainee),
            'record' => $activeTrainee,
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Set or update active trainee telemetry in cache during active VR drill.
     *
     * POST /api/active-trainee
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setActiveTrainee(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: json_decode($request->getContent(), true) ?: $request->all();
        $username = trim($payload['username'] ?? $request->input('username', ''));

        if (empty($username) || $username === 'none') {
            Cache::forget('active_vr_trainee');
            return response()->json([
                'status' => 'cleared',
                'has_active' => false,
                'record' => null,
            ]);
        }

        $existing = TraineeResult::where('username', $username)->latest()->first();

        $activeData = [
            'username' => $username,
            'score' => (float) ($payload['score'] ?? $request->input('score', $existing ? $existing->score : 0.0)),
            'hazards_found' => (int) ($payload['hazards_found'] ?? $request->input('hazards_found', $existing ? $existing->hazards_found : 0)),
            'hazards_missed' => (int) ($payload['hazards_missed'] ?? $request->input('hazards_missed', $existing ? $existing->hazards_missed : 13)),
            'wrong_clicks' => (int) ($payload['wrong_clicks'] ?? $request->input('wrong_clicks', $existing ? $existing->wrong_clicks : 0)),
            'found_hazards' => $payload['found_hazards'] ?? $request->input('found_hazards', $existing ? $existing->found_hazards : []),
            'completion_time' => (float) ($payload['completion_time'] ?? $request->input('completion_time', $existing ? $existing->completion_time : 0)),
            'active_at' => now()->toIso8601String(),
        ];

        Cache::put('active_vr_trainee', $activeData, now()->addHours(2));

        return response()->json([
            'status' => 'success',
            'has_active' => true,
            'record' => $activeData,
        ]);
    }

    /**
     * Clear the active VR trainee when simulation ends.
     *
     * POST /api/active-trainee/clear
     *
     * @return JsonResponse
     */
    public function clearActiveTrainee(): JsonResponse
    {
        Cache::forget('active_vr_trainee');

        return response()->json([
            'status' => 'cleared',
            'has_active' => false,
            'record' => null,
        ]);
    }

    // =========================================================================
    // SECTION 2: WEB DASHBOARD VIEWS
    // =========================================================================

    /**
     * Operations Command Center (Overview Page - 5 Recent Records & Key KPIs)
     *
     * GET /overview or GET /dashboard
     *
     * @return View
     */
    public function index(): View
    {
        $records = TraineeResult::latest()->take(5)->get();
        $totalSessions = TraineeResult::count();
        $avgScore = TraineeResult::avg('score') ?? 0;
        $avgTime = TraineeResult::avg('completion_time') ?? 0;
        $totalHazardsFound = TraineeResult::sum('hazards_found');

        return view('dashboard', compact(
            'records',
            'totalSessions',
            'avgScore',
            'avgTime',
            'totalHazardsFound'
        ));
    }

    /**
     * Live VR Session Monitor Page (Meta Quest Developer Hub Cast & Telemetry)
     *
     * GET /monitor
     *
     * @return View
     */
    public function monitor(): View
    {
        $activeTrainee = Cache::get('active_vr_trainee');
        if (!$activeTrainee) {
            $latest = TraineeResult::latest()->first();
            if ($latest) {
                $activeTrainee = [
                    'username' => $latest->username,
                    'hazards_found' => (int) $latest->hazards_found,
                    'wrong_clicks' => (int) $latest->wrong_clicks,
                    'score' => (float) $latest->score,
                    'completion_time' => (float) $latest->completion_time,
                    'found_hazards' => $latest->found_hazards,
                ];
            }
        }

        $warehouseAreas = $this->evaluationService->getWarehouseAreasConfig();

        $allHazards = [];
        foreach ($warehouseAreas as $area) {
            foreach ($area['hazards'] as $h) {
                $allHazards[] = [
                    'id' => $h['id'],
                    'name' => $h['name'],
                    'area_name' => $area['name'],
                    'area_icon' => $area['icon'],
                    'severity' => $h['severity'],
                    'aliases' => $h['aliases'] ?? [],
                ];
            }
        }

        return view('monitor', compact('activeTrainee', 'allHazards'));
    }

    /**
     * Trainee Directory & Leaderboard Page (Search, 10-Item Pagination, Filtering)
     *
     * GET /directory
     *
     * @return View
     */
    public function directory(): View
    {
        // Order by Leaderboard: Highest score first, then fastest completion time
        $records = TraineeResult::orderByDesc('score')->orderBy('completion_time')->get();

        // Extract unique formatted dates for date dropdown filtering
        $availableDates = TraineeResult::select('created_at')
            ->latest()
            ->get()
            ->map(fn($r) => $r->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y'))
            ->unique()
            ->values();

        return view('directory', compact('records', 'availableDates'));
    }

    /**
     * Experience Analytics Page (6-Area Vulnerability & Blindspot Detection)
     *
     * GET /analytics
     *
     * @return View
     */
    public function analytics(): View
    {
        $data = $this->evaluationService->calculateAreaAnalytics();
        return view('analytics', $data);
    }

    /**
     * Evaluation Protocol Guide Page (Methodology & Performance Tier Benchmarks)
     *
     * GET /guide
     *
     * @return View
     */
    public function guide(): View
    {
        $totalSessions = TraineeResult::count();
        $certifiedCount = TraineeResult::where('is_certified', true)->count();
        $retestCount = max(0, $totalSessions - $certifiedCount);
        $passRate = $totalSessions > 0 ? round(($certifiedCount / $totalSessions) * 100, 1) : 0.0;
        $avgScore = round(TraineeResult::avg('score') ?? 0.0, 1);
        $avgTime = TraineeResult::avg('completion_time') ?? 0;
        $avgMinutes = floor($avgTime / 60);
        $avgSeconds = round(fmod($avgTime, 60));

        // Distribution by tier
        $masterCount = TraineeResult::where('score', '>=', 90.0)->count();
        $greatCount = TraineeResult::where('score', '>=', 80.0)->where('score', '<', 90.0)->count();
        $validCount = TraineeResult::where('score', '>=', 60.0)->where('score', '<', 80.0)->count();
        $needsReviewCount = TraineeResult::where('score', '<', 60.0)->count();

        $tierStats = [
            'locked_in' => [
                'count' => $masterCount,
                'rate' => $totalSessions > 0 ? round(($masterCount / $totalSessions) * 100, 1) : 0.0,
            ],
            'great' => [
                'count' => $greatCount,
                'rate' => $totalSessions > 0 ? round(($greatCount / $totalSessions) * 100, 1) : 0.0,
            ],
            'valid_effort' => [
                'count' => $validCount,
                'rate' => $totalSessions > 0 ? round(($validCount / $totalSessions) * 100, 1) : 0.0,
            ],
            'needs_review' => [
                'count' => $needsReviewCount,
                'rate' => $totalSessions > 0 ? round(($needsReviewCount / $totalSessions) * 100, 1) : 0.0,
            ],
        ];

        $warehouseAreas = $this->evaluationService->getWarehouseAreasConfig();

        return view('guide', compact(
            'totalSessions',
            'certifiedCount',
            'retestCount',
            'passRate',
            'avgScore',
            'avgMinutes',
            'avgSeconds',
            'tierStats',
            'warehouseAreas'
        ));
    }

    /**
     * Trainee Personal Profile with 6-Area Breakdown & Supervisor Statement
     *
     * GET /trainee/{id}
     *
     * @param string|int $id
     * @return View
     */
    public function show($id): View
    {
        $record = TraineeResult::where('id', $id)
            ->orWhere('username', $id)
            ->firstOrFail();

        $areaBreakdown = $this->evaluationService->calculateTraineeAreaBreakdown($record);
        $statement = $this->evaluationService->generatePerformanceStatement($record, $areaBreakdown);

        // Overall ranking on the leaderboard
        $allRecords = TraineeResult::orderByDesc('score')->orderBy('completion_time')->get();
        $rankIndex = $allRecords->search(fn($r) => $r->id === $record->id);
        $rankNumber = $rankIndex !== false ? $rankIndex + 1 : 1;
        $totalTrainees = $allRecords->count();

        return view('trainee', compact(
            'record',
            'areaBreakdown',
            'statement',
            'rankNumber',
            'totalTrainees'
        ));
    }

    /**
     * Supervisor Login Page
     *
     * GET / or GET /login
     *
     * @return View
     */
    public function login(): View
    {
        return view('login');
    }

    // =========================================================================
    // SECTION 3: BACKWARD-COMPATIBLE PROXY METHODS
    // (Delegates to HazardEvaluationService for legacy callers)
    // =========================================================================

    public function getWarehouseAreasConfig(): array
    {
        return $this->evaluationService->getWarehouseAreasConfig();
    }

    public function calculateAreaAnalytics(): array
    {
        return $this->evaluationService->calculateAreaAnalytics();
    }

    public function calculateTraineeAreaBreakdown(TraineeResult $record): array
    {
        return $this->evaluationService->calculateTraineeAreaBreakdown($record);
    }

    public function generatePerformanceStatement(TraineeResult $record, array $areaBreakdown): array
    {
        return $this->evaluationService->generatePerformanceStatement($record, $areaBreakdown);
    }
}
