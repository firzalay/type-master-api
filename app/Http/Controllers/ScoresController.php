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
        $subquery = DB::table('user_scores')
            ->select('user_id', DB::raw('MAX(wpm_score) as max_score'), DB::raw('MAX(accuracy) as max_accuracy'))
            ->groupBy('user_id');

        $scores = DB::table('users')
            ->joinSub($subquery, 'subquery', function ($join) {
                $join->on('users.id', '=', 'subquery.user_id');
            })
            ->select('users.name', 'subquery.max_score as wpm_score', 'subquery.max_accuracy as accuracy')
            ->orderBy('subquery.max_score', 'desc')
            ->limit(25)
            ->get();

        return response()->json($scores);
    }
}
