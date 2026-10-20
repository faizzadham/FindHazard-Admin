<?php

namespace App\Services;

use App\Models\TraineeResult;

/**
 * HazardEvaluationService
 *
 * Dedicated service class responsible for:
 * 1. Master configuration of the 6 Warehouse Zones & 13 Safety Hazards.
 * 2. Cross-trainee area analytics and blindspot vulnerability metrics.
 * 3. Individual trainee zone breakdown and detection percentages.
 * 4. Automated supervisory evaluation statement and tier grading generation.
 */
class HazardEvaluationService
{
    /**
     * Master configuration for the 6 warehouse areas and their 13 safety hazards.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getWarehouseAreasConfig(): array
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
     * Compute comprehensive hazard detection statistics grouped by the 6 warehouse areas.
     *
     * @return array{areaAnalytics: array, summary: array}
     */
    public function calculateAreaAnalytics(): array
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
     * Compute individual hazard detection breakdown for a specific trainee across all 6 warehouse areas.
     *
     * @param TraineeResult $record
     * @return array<string, array<string, mixed>>
     */
    public function calculateTraineeAreaBreakdown(TraineeResult $record): array
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
     *
     * @param TraineeResult $record
     * @param array $areaBreakdown
     * @return array<string, mixed>
     */
    public function generatePerformanceStatement(TraineeResult $record, array $areaBreakdown): array
    {
        $score = (float) $record->score;
        $isCertified = (bool) $record->is_certified;
        $wrongClicks = (int) $record->wrong_clicks;
        $foundCount = (int) $record->hazards_found;
        $missedCount = (int) $record->hazards_missed;
        $totalHazards = 13;
        $penaltyPercent = $wrongClicks * 5;

        $timeMins = floor($record->completion_time / 60);
        $timeSecs = round(fmod($record->completion_time, 60));
        $timeFormatted = "{$timeMins}m " . sprintf('%02ds', $timeSecs);
        $scoreFormatted = number_format($score, 1) . '%';

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

        $allDeficient = array_merge($partialAreas, $deficientAreas);
        $deficientText = !empty($allDeficient) ? ' in ' . implode(', ', $allDeficient) : '';

        // Formulate misclick phrasing
        $misclickPhrase = match (true) {
            $wrongClicks === 0 => 'flawless trigger discipline with zero false-alarm misclicks',
            $wrongClicks === 1 => '1 safe-object distraction click (-5% penalty)',
            default            => "{$wrongClicks} safe-object distraction clicks (-{$penaltyPercent}% penalty)",
        };

        // Formulate hazard detection phrasing
        $detectionPhrase = match (true) {
            $missedCount === 0 => "all {$totalHazards} warehouse hazards across all " . count($areaBreakdown) . " operational zones (100% detection coverage)",
            $foundCount === 0  => "zero warehouse hazards",
            default            => "{$foundCount} out of {$totalHazards} hazards",
        };

        // Determine Evaluation Tier & Verdict
        if ($score >= 100.0 && $wrongClicks === 0) {
            $grade = 'Grade A+ (Elite)';
            $gradeBadgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-500/20';
            $headline = 'Exemplary Master Inspector — Flawless Warehouse Hazard Detection';
            $verdictType = 'success';
            $narrative = "Trainee {$record->username} delivered a flawless VR warehouse inspection with an extraordinary 100.0% score in {$timeFormatted}. Demonstrating supreme situational awareness, zero false-alarm misclicks were triggered, and all {$totalHazards} critical warehouse hazards across all 6 operational zones were identified with high precision. Fully verified for unrestricted aviation cargo operations.";

        } elseif ($score >= 90.0) {
            $grade = 'Grade A (Superior)';
            $gradeBadgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
            $headline = 'Distinguished Lead Inspector — High Proficiency Across Operational Zones';
            $verdictType = 'success';
            $penaltyText = $wrongClicks > 0
                ? " A minor penalty of -{$penaltyPercent}% was incurred for {$misclickPhrase}."
                : " Flawless click discipline with zero misclicks recorded.";
            $narrative = "Trainee {$record->username} demonstrated superior inspection competency, securing a {$scoreFormatted} overall score in {$timeFormatted}. The trainee detected {$detectionPhrase} with complete mastery in " . count($perfectAreas) . " zones.{$penaltyText} Strongly recommended for active warehouse safety certification.";

        } elseif ($score >= 80.0) {
            $grade = 'Grade B+ (Compliant)';
            $gradeBadgeClass = 'bg-teal-500/15 text-teal-300 border-teal-500/30';
            $headline = 'Safety Standard Met — Certified for Warehouse Operations';
            $verdictType = 'info';
            $penaltyText = $wrongClicks > 0
                ? " Noted {$misclickPhrase}."
                : " Satisfactory trigger control with 0 distraction clicks.";
            $narrative = "Trainee {$record->username} successfully satisfied safety qualification thresholds, earning a certified score of {$scoreFormatted} in {$timeFormatted}. The inspection isolated {$detectionPhrase}.{$penaltyText} Candidate possesses satisfactory situational awareness to operate safely under supervisor guidance.";

        } elseif ($score >= 70.0) {
            $grade = 'Grade C (Conditional)';
            $gradeBadgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
            $verdictType = 'warning';

            if ($missedCount === 0) {
                // All hazards spotted, but pulled down by misclicks
                $headline = 'Retest Required — Flawless Detection Compromised by Misclick Penalties';
                $narrative = "Trainee {$record->username} recorded a score of {$scoreFormatted}, narrowly missing the 80.0% passing benchmark. While all {$totalHazards} hazards were successfully recognized (100% detection coverage) in {$timeFormatted}, {$misclickPhrase} reduced the final score below the qualification mark. Candidate exhibits complete hazard perception; targeted trigger discipline practice is required prior to drill reassessment.";
            } else {
                $headline = 'Retest Mandatory — Marginal Deficiency Below Qualification Threshold';
                $gapsText = !empty($deficientAreas) ? " Overlooked critical hazards in " . implode(', ', $deficientAreas) . "." : "";
                $penaltyText = $wrongClicks > 0 ? " Misclick penalties (-{$penaltyPercent}%) compounded the score deficit." : "";
                $missedPhrase = $missedCount === 1 ? '1 vital risk was bypassed' : "{$missedCount} vital risks were bypassed";
                $narrative = "Trainee {$record->username} recorded a score of {$scoreFormatted}, narrowly missing the 80.0% passing mark. While {$foundCount} hazards were recognized in {$timeFormatted}, {$missedPhrase}.{$gapsText}{$penaltyText} Qualification cannot be approved at this time; a targeted zone re-drill is mandatory.";
            }

        } else {
            // Score < 70.0% (Grade D)
            $gradeBadgeClass = 'bg-rose-500/15 text-rose-300 border-rose-500/30';
            $verdictType = 'danger';

            if ($missedCount === 0) {
                // Special case: 100% of hazards found across all zones, but penalized by wrong clicks
                $grade = 'Grade D (Trigger Discipline Review)';
                $headline = 'Trigger Discipline Deficit — 100% Hazard Coverage Compromised by False Alarms';
                $narrative = "Trainee {$record->username} demonstrated comprehensive visual hazard recognition, successfully identifying all {$totalHazards} warehouse hazards across all 6 operational zones in {$timeFormatted}. However, the final score fell to {$scoreFormatted} strictly due to {$misclickPhrase}. While the candidate possesses complete hazard awareness, intensive trigger discipline and false-alarm prevention training is required prior to drill reassessment.";
            } elseif ($foundCount >= 10 && $wrongClicks >= 4) {
                // High detection coverage (>= 10/13), but severely penalized by misclicks
                $grade = 'Grade D (Trigger Discipline Review)';
                $headline = 'Trigger Discipline Deficit — High Hazard Detection Compromised by False Alarms';
                $narrative = "Trainee {$record->username} demonstrated solid hazard detection, recognizing {$foundCount} out of {$totalHazards} hazards across " . count($perfectAreas) . " zones in {$timeFormatted}. However, the score dropped to {$scoreFormatted} primarily due to {$misclickPhrase}. Candidate must practice deliberate confirmation before clicking to eliminate safe-object false alarms prior to drill reassessment.";
            } elseif ($wrongClicks <= 2) {
                // Pure hazard detection deficiency (good trigger discipline, but missed hazards)
                $grade = 'Grade D (Needs Review)';
                $missedPhrase = $missedCount === 1 ? '1 hazard went unidentified' : "{$missedCount} hazards went unidentified";
                $headline = "Action Required — {$missedCount} Safety Hazards Overlooked in VR Drill";
                $narrative = "Trainee {$record->username} scored {$scoreFormatted}, falling substantially below the 80.0% safety benchmark. {$missedPhrase}{$deficientText} in {$timeFormatted}, despite disciplined trigger control ({$wrongClicks} misclick(s)). Intensive safety retraining across overlooked freight handling bays is required prior to drill reassessment.";
            } else {
                // Composite deficiency: Both missed hazards AND high misclicks
                $grade = 'Grade D (Needs Review)';
                $missedPhrase = $missedCount === 1 ? '1 hazard went unidentified' : "{$missedCount} hazards went unidentified";
                $headline = 'Action Required — Composite Hazard Oversight and False-Alarm Penalties';
                $narrative = "Trainee {$record->username} scored {$scoreFormatted}, falling substantially below the 80.0% safety benchmark. {$missedPhrase}{$deficientText} in {$timeFormatted}, alongside {$misclickPhrase}. Comprehensive safety retraining covering both zone hazard identification and click precision is required prior to drill reassessment.";
            }
        }

        return [
            'grade' => $grade,
            'grade_badge_class' => $gradeBadgeClass,
            'headline' => $headline,
            'narrative' => $narrative,
            'verdict_type' => $verdictType,
            'perfect_areas' => $perfectAreas,
            'deficient_areas' => $allDeficient,
            'wrong_clicks_commentary' => $wrongClicks === 0
                ? "100% Trigger Discipline (0 safe objects misclicked)"
                : "{$wrongClicks} Distraction Misclick(s) (-{$penaltyPercent}% score penalty applied)",
            'time_commentary' => "Completed full warehouse scan in {$timeFormatted} (" . number_format($record->completion_time, 1) . "s total velocity)",
        ];
    }
}
