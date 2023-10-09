<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoresController extends Controller
{
    public function store(Request $request)
    {
        $userId = $request->input('user_id');
        $wpmScore = $request->input('wpm_score');
        $accuracy = $request->input('accuracy');

        UserScore::create([
            'user_id' => $userId,
            'wpm_score' => $wpmScore,
            'accuracy' => $accuracy,
        ]);

        return response()->json(['message' => 'Score stored successfully'], 201);
    }

    public function topScores()
    {
        $scores = DB::table('users')
            ->leftJoin('user_scores', 'users.id', '=', 'user_scores.user_id')
            ->select('users.name', DB::raw('MAX(user_scores.wpm_score) as max_score'), DB::raw('MAX(user_scores.accuracy) as max_accuracy'))
            ->groupBy('users.id')
            ->orderBy('max_score', 'desc')
            ->limit(25)
            ->get();

        return response()->json($scores);
    }
}
