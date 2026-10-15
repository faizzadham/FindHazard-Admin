<?php

namespace Database\Seeders;

use App\Models\TraineeResult;
use Illuminate\Database\Seeder;

class TraineeResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allHazards = [
            'Tarmac Foreign Object Debris (FOD)',
            'Dislodged Restraint Net on PMC Pallet',
            'Fluid & Oil Leak Slip Hazard',
            'Dislodged Aircraft Parts',
            'Unstable Cargo Stack',
            'Blocked Emergency Exit',
            'Overloaded Forklift',
            'Unsecured Boxes',
            'Improperly Stacked ULD Container',
            'Damaged ULD',
            'Exposed Power Cable',
            'Accumulated Packaging Debris',
            'Crushed Bottom Pallet Base'
        ];

        $trainees = [
            [
                'username' => 'sayig',
                'score' => 100.0,
                'hazards_found' => 13,
                'hazards_missed' => 0,
                'wrong_clicks' => 0,
                'completion_time' => 78.5,
                'performance_rating' => 'Locked In',
                'is_certified' => true,
                'found_hazards' => $allHazards,
                'missed_hazards' => [],
            ],
            [
                'username' => 'amir_inspector',
                'score' => 92.3,
                'hazards_found' => 12,
                'hazards_missed' => 1,
                'wrong_clicks' => 0,
                'completion_time' => 84.2,
                'performance_rating' => 'Locked In',
                'is_certified' => true,
                'found_hazards' => array_values(array_diff($allHazards, ['Damaged ULD'])),
                'missed_hazards' => ['Damaged ULD'],
            ],
            [
                'username' => 'faiz',
                'score' => 84.6,
                'hazards_found' => 11,
                'hazards_missed' => 2,
                'wrong_clicks' => 0,
                'completion_time' => 92.0,
                'performance_rating' => 'Great',
                'is_certified' => true,
                'found_hazards' => array_values(array_diff($allHazards, ['Crushed Bottom Pallet Base', 'Dislodged Aircraft Parts'])),
                'missed_hazards' => ['Crushed Bottom Pallet Base', 'Dislodged Aircraft Parts'],
            ],
            [
                'username' => 'hafiz_pilot',
                'score' => 79.6,
                'hazards_found' => 11,
                'hazards_missed' => 2,
                'wrong_clicks' => 1,
                'completion_time' => 105.4,
                'performance_rating' => 'Valid Effort',
                'is_certified' => false,
                'found_hazards' => array_values(array_diff($allHazards, ['Damaged ULD', 'Crushed Bottom Pallet Base'])),
                'missed_hazards' => ['Damaged ULD', 'Crushed Bottom Pallet Base'],
            ],
            [
                'username' => 'quest3_pilot',
                'score' => 61.5,
                'hazards_found' => 8,
                'hazards_missed' => 5,
                'wrong_clicks' => 0,
                'completion_time' => 118.0,
                'performance_rating' => 'Valid Effort',
                'is_certified' => false,
                'found_hazards' => array_values(array_diff($allHazards, ['Damaged ULD', 'Crushed Bottom Pallet Base', 'Exposed Power Cable', 'Dislodged Aircraft Parts', 'Unstable Cargo Stack'])),
                'missed_hazards' => ['Damaged ULD', 'Crushed Bottom Pallet Base', 'Exposed Power Cable', 'Dislodged Aircraft Parts', 'Unstable Cargo Stack'],
            ],
        ];

        foreach ($trainees as $data) {
            TraineeResult::firstOrCreate(
                ['username' => $data['username']],
                $data
            );
        }
    }
}
