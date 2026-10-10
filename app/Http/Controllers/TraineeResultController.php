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
            'found_hazards' => 'nullable',
            'missed_hazards' => 'nullable',
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
            'score' => $validated['score'],
            'hazards_found' => $validated['hazards_found'],
            'hazards_missed' => $validated['hazards_missed'],
            'found_hazards' => $foundHazards,
            'missed_hazards' => $missedHazards,
            'completion_time' => $validated['completion_time'],
            'performance_rating' => $rating,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Trainee score recorded successfully',
            'data' => $record
        ], 201);
    }

    // Web Route: Renders Operations Command Center (Overview Page)
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

    // Web Route: Renders Live VR Session Monitor Page
    public function monitor()
    {
        return view('monitor');
    }

    // Web Route: Renders Trainee Directory Page
    public function directory()
    {
        $records = TraineeResult::latest()->get();
        return view('directory', compact('records'));
    }

    // Web Route: Renders Trainee Analysis & Checkpoints Page
    public function analysis(Request $request)
    {
        $traineeId = $request->query('trainee', 'qq');
        return view('analysis', compact('traineeId'));
    }

    // Web Route: Renders Experience Analytics Page
    public function analytics()
    {
        return view('analytics');
    }

    // Web Route: Renders Supervisor Login Page
    public function login()
    {
        return view('login');
    }
}
