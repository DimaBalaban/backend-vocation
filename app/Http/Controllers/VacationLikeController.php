<?php

namespace App\Http\Controllers;


use App\Models\VacationLike;
use Illuminate\Http\Request;

class VacationLikeController extends Controller
{
    public function store(Request $request)
    {

        $vacationId = $request->input('vacation_id');
        $userId = $request->input('user_id');

         if (!$userId) {
            return response()->json(['error' => 'User ID is missing'], 400);
        }

        $existingLike = VacationLike::where('vacation_id', $vacationId)
                                    ->where('user_id', $userId)
                                    ->first();

        if ($existingLike) {
            return response()->json(['message' => 'Like already exists', 'like_count' => $this->getLikeCount($vacationId)]);
        }

        $like = new VacationLike();
        $like->vacation_id = $vacationId;
        $like->user_id = $userId;
        $like->save();

        return response()->json(['message' => 'Like added', 'like_count' => $this->getLikeCount($vacationId)]);
    }
public function getLikeCount($vacationId)
    {
        return VacationLike::where('vacation_id', $vacationId)->count();
    }

    public function getLikesForVacation($vacationId)
    {
        return response()->json(['like_count' => $this->getLikeCount($vacationId)]);
        }
}
