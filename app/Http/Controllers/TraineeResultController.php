<?php

namespace App\Http\Controllers;

use App\Models\TraineeResult;
use Illuminate\Http\Request;

class TraineeResultController extends Controller
{
    // API Route: Receives session payload from Unity VR
    public function store(Request $request)
    {
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

        // Certificate Qualification Logic:
        // Trainee qualifies for VR Safety Certificate if final score >= 80.0%
        $isCertified = ($finalScore >= 80.0);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Trainee session evaluated and recorded successfully',
            'data' => $record
        ], 201);
    }

    // Web Route: Renders Operations Command Center (Overview Page - 5 Recent Records)
    public function index()
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

    // Web Route: Renders Live VR Session Monitor Page
    public function monitor()
    {
        return view('monitor');
    }

    // Web Route: Renders Trainee Directory Page (Leaderboard & Telemetry)
    public function directory()
    {
        // Order by Leaderboard: Highest score first, then fastest completion time
        $records = TraineeResult::orderByDesc('score')->orderBy('completion_time')->get();

        // Extract unique formatted dates for date filtering
        $availableDates = TraineeResult::select('created_at')
            ->latest()
            ->get()
            ->map(fn($r) => $r->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y'))
            ->unique()
            ->values();

        return view('directory', compact('records', 'availableDates'));
    }

    // Web Route: Renders Experience Analytics Page
    public function analytics()
    {
        $data = $this->calculateAreaAnalytics();
        return view('analytics', $data);
    }

    // Web Route: Renders Evaluation Guide Page
    public function guide()
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

        $warehouseAreas = $this->getWarehouseAreasConfig();

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

    // Web Route: Renders Trainee Personal Profile with 6-Area Breakdown & Statement
    public function show($id)
    {
        $record = TraineeResult::where('id', $id)
            ->orWhere('username', $id)
            ->firstOrFail();

        $areaBreakdown = $this->calculateTraineeAreaBreakdown($record);
        $statement = $this->generatePerformanceStatement($record, $areaBreakdown);

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
     * Compute comprehensive hazard detection statistics grouped by the 6 warehouse areas.
     */
    private function calculateAreaAnalytics(): array
    {
        $areasConfig = $this->getWarehouseAreasConfig();

        $records = TraineeResult::latest()->get();
        $totalTrainees = max(1, $records->count());

        $areaAnalytics = [];
        $totalWarehouseDetections = 0;
        $totalWarehouseOpportunities = 0;

        foreach ($areasConfig as $areaKey => $area) {
            $areaFoundTotal = 0;
            $areaPossible = count($area['hazards']) * $records->count();
            $hazardsData = [];

            foreach ($area['hazards'] as $h) {
                $foundCount = 0;
                foreach ($records as $r) {
                    $foundList = $r->found_hazards ?? [];
                    $isFound = false;
                    if (!empty($foundList)) {
                        foreach ($foundList as $item) {
                            $itemLower = strtolower($item);
                            foreach ($h['aliases'] as $alias) {
                                if (str_contains($itemLower, strtolower($alias))) {
                                    $isFound = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    if ($isFound) {
                        $foundCount++;
                    }
                }

                $detectionRate = round(($foundCount / $totalTrainees) * 100, 1);
                $missedCount = max(0, $records->count() - $foundCount);
                $status = match (true) {
                    $detectionRate >= 80.0 => 'Optimal',
                    $detectionRate >= 60.0 => 'Moderate',
                    default => 'Blindspot',
                };

                $hazardsData[] = [
                    'id' => $h['id'],
                    'name' => $h['name'],
                    'severity' => $h['severity'],
                    'found_count' => $foundCount,
                    'missed_count' => $missedCount,
                    'detection_rate' => $detectionRate,
                    'status' => $status,
                ];

                $areaFoundTotal += $foundCount;
            }

            $areaRate = $areaPossible > 0 ? round(($areaFoundTotal / $areaPossible) * 100, 1) : 0.0;
            $areaStatus = match (true) {
                $areaRate >= 80.0 => 'Optimal',
                $areaRate >= 65.0 => 'Satisfactory',
                default => 'Vulnerability',
            };

            $areaAnalytics[$areaKey] = array_merge($area, [
                'total_hazards' => count($area['hazards']),
                'total_found' => $areaFoundTotal,
                'total_possible' => $areaPossible,
                'detection_rate' => $areaRate,
                'status' => $areaStatus,
                'hazards' => $hazardsData,
            ]);

            $totalWarehouseDetections += $areaFoundTotal;
            $totalWarehouseOpportunities += $areaPossible;
        }

        // Identify Best and Worst areas
        $sortedAreas = collect($areaAnalytics)->sortByDesc('detection_rate');
        $bestArea = $sortedAreas->first();
        $worstArea = $sortedAreas->last();

        $overallRate = $totalWarehouseOpportunities > 0
            ? round(($totalWarehouseDetections / $totalWarehouseOpportunities) * 100, 1)
            : 0.0;

        $certifiedCount = $records->where('is_certified', true)->count();
        $certifiedRate = $records->count() > 0 ? round(($certifiedCount / $records->count()) * 100, 1) : 0.0;

        $summary = [
            'totalTrainees' => $records->count(),
            'overallDetectionRate' => $overallRate,
            'totalHazardsIdentified' => $totalWarehouseDetections,
            'totalOpportunities' => $totalWarehouseOpportunities,
            'bestArea' => $bestArea,
            'worstArea' => $worstArea,
            'certifiedCount' => $certifiedCount,
            'certifiedRate' => $certifiedRate,
        ];

        return compact('areaAnalytics', 'summary');
    }

    /**
     * Master configuration for the 6 warehouse areas and their 13 safety hazards.
     */
    private function getWarehouseAreasConfig(): array
    {
        return [
            'aircraft_parts' => [
                'name' => 'Aircraft Parts Storage Area',
                'slug' => 'aircraft-parts-storage',
                'icon' => '✈️',
                'tag' => 'Parts & Fluids',
                'color' => 'sky',
                'description' => 'Precision aviation parts inventory, component staging, and fluid slip containment.',
                'hazards' => [
                    [
                        'id' => 1,
                        'name' => 'Fluid & Oil Leak Slip Hazard',
                        'aliases' => ['fluid', 'oil leak', 'slip hazard'],
                        'severity' => 'High Slip Risk',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Dislodged Aircraft Parts',
                        'aliases' => ['dislodged aircraft parts', 'dislodges aircraft parts', 'spilled aircraft parts', 'aircraft parts assembly'],
                        'severity' => 'Foreign Material',
                    ],
                ],
            ],
            'cargo_storage' => [
                'name' => 'Cargo Storage Area',
                'slug' => 'cargo-storage',
                'icon' => '📦',
                'tag' => 'Staging & Egress',
                'color' => 'amber',
                'description' => 'General palletized cargo warehouse, wooden crates, and designated evacuation corridors.',
                'hazards' => [
                    [
                        'id' => 3,
                        'name' => 'Unstable Cargo Stack',
                        'aliases' => ['unstable cargo', 'wooden crate', 'cargo stack', 'cargo wooden crate'],
                        'severity' => 'Stack Collapse',
                    ],
                    [
                        'id' => 4,
                        'name' => 'Blocked Emergency Exit',
                        'aliases' => ['blocked emergency', 'obstructed emergency', 'emergency exit', 'fire exit'],
                        'severity' => 'Life Safety Egress',
                    ],
                ],
            ],
            'main_cargo_handling' => [
                'name' => 'Main Cargo Handling Area',
                'slug' => 'main-cargo-handling',
                'icon' => '🚜',
                'tag' => 'Active Traffic',
                'color' => 'orange',
                'description' => 'Active forklift transport corridors, high-bay racking systems, and box staging.',
                'hazards' => [
                    [
                        'id' => 5,
                        'name' => 'Overloaded Forklift',
                        'aliases' => ['overloaded forklift', 'forklift in operation', 'forklift'],
                        'severity' => 'Mobile Heavy Equipment',
                    ],
                    [
                        'id' => 6,
                        'name' => 'Unsecured Boxes',
                        'aliases' => ['unsecured boxes', 'unsecured cargo boxes', 'boxes on high shelf'],
                        'severity' => 'Falling Object Hazard',
                    ],
                ],
            ],
            'uld_storage' => [
                'name' => 'ULD Storage Area',
                'slug' => 'uld-storage',
                'icon' => '📐',
                'tag' => 'Air Freight ULD',
                'color' => 'indigo',
                'description' => 'Unit Load Device (ULD) container staging, airworthiness integrity, and vertical stacking bays.',
                'hazards' => [
                    [
                        'id' => 7,
                        'name' => 'Improperly Stacked ULD Container',
                        'aliases' => ['improperly stacked uld', 'stacked uld'],
                        'severity' => 'Structural Instability',
                    ],
                    [
                        'id' => 8,
                        'name' => 'Damaged ULD',
                        'aliases' => ['damaged uld', 'damaged uld container wall', 'container wall'],
                        'severity' => 'Airworthiness Integrity',
                    ],
                ],
            ],
            'loading_unloading' => [
                'name' => 'Loading/Unloading Area',
                'slug' => 'loading-unloading',
                'icon' => '⚡',
                'tag' => 'Dock & Power Bay',
                'color' => 'yellow',
                'description' => 'High-activity freight transfer bays, electrical conduits, combustible waste, and base pallet stability.',
                'hazards' => [
                    [
                        'id' => 9,
                        'name' => 'Exposed Power Cable',
                        'aliases' => ['exposed power cable', 'damaged electrical cable', 'cable branch wire', 'power cable'],
                        'severity' => 'Electrical Arc Hazard',
                    ],
                    [
                        'id' => 10,
                        'name' => 'Accumulated Packaging Debris',
                        'aliases' => ['accumulated packaging debris', 'packaging debris', 'debris'],
                        'severity' => 'Combustible Waste',
                    ],
                    [
                        'id' => 11,
                        'name' => 'Crushed Bottom Pallet Base',
                        'aliases' => ['crushed bottom pallet base', 'pallet catastrophic collapse', 'pallet collapse', 'pallet base'],
                        'severity' => 'Catastrophic Collapse',
                    ],
                ],
            ],
            'airside_cargo' => [
                'name' => 'Airside Cargo Area',
                'slug' => 'airside-cargo',
                'icon' => '🛫',
                'tag' => 'Ramp Perimeter',
                'color' => 'emerald',
                'description' => 'Active aircraft apron perimeter, PMC pallet build-up, and tarmac foreign debris prevention.',
                'hazards' => [
                    [
                        'id' => 12,
                        'name' => 'Tarmac Foreign Object Debris (FOD)',
                        'aliases' => ['tarmac foreign object debris', 'fod', 'foreign object debris'],
                        'severity' => 'Flight Safety Critical',
                    ],
                    [
                        'id' => 13,
                        'name' => 'Dislodged Restraint Net on PMC Pallet',
                        'aliases' => ['dislodged restraint net', 'pmc pallet', 'restraint net'],
                        'severity' => 'Cargo Restraint Failure',
                    ],
                ],
            ],
        ];
    }

    /**
     * Compute individual hazard detection breakdown for a specific trainee across all 6 warehouse areas.
     */
    private function calculateTraineeAreaBreakdown(TraineeResult $record): array
    {
        $areasConfig = $this->getWarehouseAreasConfig();
        $foundList = $record->found_hazards ?? [];

        $breakdown = [];

        foreach ($areasConfig as $key => $area) {
            $areaHazards = [];
            $foundCount = 0;
            $totalCount = count($area['hazards']);

            foreach ($area['hazards'] as $h) {
                $isFound = false;
                if (!empty($foundList)) {
                    foreach ($foundList as $item) {
                        $itemLower = strtolower($item);
                        foreach ($h['aliases'] as $alias) {
                            if (str_contains($itemLower, strtolower($alias))) {
                                $isFound = true;
                                break 2;
                            }
                        }
                    }
                }

                if ($isFound) {
                    $foundCount++;
                }

                $areaHazards[] = [
                    'id' => $h['id'],
                    'name' => $h['name'],
                    'severity' => $h['severity'],
                    'is_found' => $isFound,
                    'status' => $isFound ? 'Identified' : 'Overlooked',
                ];
            }

            $rate = $totalCount > 0 ? round(($foundCount / $totalCount) * 100, 1) : 0.0;

            $statusText = 'Deficient Scan';
            $statusBadgeClass = 'bg-rose-500/15 text-rose-400 border-rose-500/30';
            if ($foundCount === $totalCount) {
                $statusText = 'Flawless (100%)';
                $statusBadgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
            } elseif ($rate >= 50) {
                $statusText = 'Partial Scan';
                $statusBadgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
            }

            $breakdown[$key] = [
                'name' => $area['name'],
                'slug' => $area['slug'],
                'icon' => $area['icon'],
                'tag' => $area['tag'],
                'color' => $area['color'],
                'description' => $area['description'],
                'hazards' => $areaHazards,
                'found_count' => $foundCount,
                'total_count' => $totalCount,
                'rate' => $rate,
                'status_text' => $statusText,
                'status_badge_class' => $statusBadgeClass,
            ];
        }

        return $breakdown;
    }

    /**
     * Generate comprehensive automated supervisor statement based on personal performance.
     */
    private function generatePerformanceStatement(TraineeResult $record, array $areaBreakdown): array
    {
        $score = (float) $record->score;
        $isCertified = (bool) $record->is_certified;
        $wrongClicks = (int) $record->wrong_clicks;
        $foundCount = (int) $record->hazards_found;
        $missedCount = (int) $record->hazards_missed;
        $timeMins = floor($record->completion_time / 60);
        $timeSecs = round(fmod($record->completion_time, 60));
        $timeFormatted = "{$timeMins}m " . sprintf('%02ds', $timeSecs);

        $perfectAreas = [];
        $partialAreas = [];
        $deficientAreas = [];

        foreach ($areaBreakdown as $area) {
            if ($area['found_count'] === $area['total_count']) {
                $perfectAreas[] = $area['name'];
            } elseif ($area['rate'] >= 50) {
                $partialAreas[] = "{$area['name']} ({$area['found_count']}/{$area['total_count']})";
            } else {
                $deficientAreas[] = "{$area['name']} ({$area['found_count']}/{$area['total_count']})";
            }
        }

        // Tier Grade & Verdict
        if ($score >= 100.0 && $wrongClicks === 0) {
            $grade = 'Grade A+ (Elite)';
            $gradeBadgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-500/20';
            $headline = 'Exemplary Master Inspector — Flawless Warehouse Hazard Detection';
            $verdictType = 'success';
            $narrative = "Trainee {$record->username} delivered a flawless VR warehouse inspection with an extraordinary 100.0% score in {$timeFormatted}. Demonstrating supreme situational awareness, zero false-alarm misclicks were triggered, and all 13 critical warehouse hazards across all 6 operational zones were identified with high precision. Fully verified for unrestricted aviation cargo operations.";
        } elseif ($score >= 90.0) {
            $grade = 'Grade A (Superior)';
            $gradeBadgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
            $headline = 'Distinguished Lead Inspector — High Proficiency Across Operational Zones';
            $verdictType = 'success';
            $penaltyText = $wrongClicks > 0 ? " A minor penalty of -" . ($wrongClicks * 5) . "% was incurred for {$wrongClicks} safe-object distraction click(s)." : " Flawless click discipline with zero misclicks recorded.";
            $narrative = "Trainee {$record->username} demonstrated superior inspection competency, securing a " . number_format($score, 1) . "% overall score in {$timeFormatted}. The trainee detected {$foundCount} out of 13 hazards with complete mastery in " . count($perfectAreas) . " zones.{$penaltyText} Strongly recommended for active warehouse safety certification.";
        } elseif ($score >= 80.0) {
            $grade = 'Grade B+ (Compliant)';
            $gradeBadgeClass = 'bg-teal-500/15 text-teal-300 border-teal-500/30';
            $headline = 'Safety Standard Met — Certified for Warehouse Operations';
            $verdictType = 'info';
            $penaltyText = $wrongClicks > 0 ? " Noted {$wrongClicks} misclick(s) resulting in a -" . ($wrongClicks * 5) . "% penalty deduction." : " Satisfactory trigger control with 0 distraction clicks.";
            $narrative = "Trainee {$record->username} successfully satisfied safety qualification thresholds, earning a certified score of " . number_format($score, 1) . "% in {$timeFormatted}. The inspection isolated {$foundCount} safety hazards across primary staging corridors.{$penaltyText} Candidate possesses satisfactory situational awareness to operate safely under supervisor guidance.";
        } elseif ($score >= 70.0) {
            $grade = 'Grade C (Conditional)';
            $gradeBadgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
            $headline = 'Retest Mandatory — Marginal Deficiency Below Qualification Threshold';
            $verdictType = 'warning';
            $gapsText = !empty($deficientAreas) ? " Overlooked critical hazards in " . implode(', ', $deficientAreas) . "." : "";
            $penaltyText = $wrongClicks > 0 ? " Misclick penalties (-" . ($wrongClicks * 5) . "%) compounded the score deficit." : "";
            $narrative = "Trainee {$record->username} recorded a score of " . number_format($score, 1) . "%, narrowly missing the 80.0% passing mark. While {$foundCount} hazards were recognized in {$timeFormatted}, {$missedCount} vital risks were bypassed.{$gapsText}{$penaltyText} Qualification cannot be approved at this time; a targeted zone re-drill is mandatory.";
        } else {
            $grade = 'Grade D (Needs Review)';
            $gradeBadgeClass = 'bg-rose-500/15 text-rose-300 border-rose-500/30';
            $headline = 'Action Required — Substantial Safety Hazards Overlooked in VR Drill';
            $verdictType = 'danger';
            $narrative = "Trainee {$record->username} scored " . number_format($score, 1) . "%, falling substantially below the 80.0% safety benchmark. {$missedCount} hazards went unidentified across multiple zones in {$timeFormatted}" . ($wrongClicks > 0 ? ", alongside {$wrongClicks} distraction click(s) (-" . ($wrongClicks * 5) . "%)" : "") . ". Intensive safety retraining across freight handling bays is required prior to drill reassessment.";
        }

        return [
            'grade' => $grade,
            'grade_badge_class' => $gradeBadgeClass,
            'headline' => $headline,
            'narrative' => $narrative,
            'verdict_type' => $verdictType,
            'perfect_areas' => $perfectAreas,
            'deficient_areas' => array_merge($partialAreas, $deficientAreas),
            'wrong_clicks_commentary' => $wrongClicks === 0
                ? "100% Trigger Discipline (0 safe objects misclicked)"
                : "{$wrongClicks} Distraction Misclick(s) (-" . ($wrongClicks * 5) . "% score penalty applied)",
            'time_commentary' => "Completed full warehouse scan in {$timeFormatted} (" . number_format($record->completion_time, 1) . "s total velocity)",
        ];
    }

    // Web Route: Renders Supervisor Login Page
    public function login()
    {
        return view('login');
    }
}
