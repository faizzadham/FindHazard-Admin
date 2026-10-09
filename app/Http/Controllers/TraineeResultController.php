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
            'score' => 'required|integer',
            'hazards_found' => 'required|integer',
            'hazards_missed' => 'required|integer',
            'completion_time' => 'required|numeric',
        ]);

        $totalHazards = $validated['hazards_found'] + $validated['hazards_missed'];
        $percentage = $totalHazards > 0 ? ($validated['hazards_found'] / $totalHazards) * 100 : 0;

        // Automated evaluation scoring logic
        $rating = match (true) {
            $percentage >= 100 => 'Locked In',
            $percentage >= 80  => 'Great',
            $percentage >= 50  => 'Valid Effort',
            default            => 'Underperforming',
        };

        $record = TraineeResult::create([
            'username' => $validated['username'],
            'score' => $validated['score'],
            'hazards_found' => $validated['hazards_found'],
            'hazards_missed' => $validated['hazards_missed'],
            'completion_time' => $validated['completion_time'],
            'performance_rating' => $rating,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Trainee score recorded successfully',
            'data' => $record
        ], 201);
    }

    // Web Route: Renders the Admin Performance Dashboard
    public function index()
    {
        $records = TraineeResult::latest()->paginate(12);
        
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
}
