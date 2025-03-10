<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacationNew;

class VacationNewController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'user_id'    => 'required|integer',
            'place'      => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'image'      => 'nullable|string',
        ]);


        $vacation = VacationNew::create([
            'user_id'    => $validatedData['user_id'],
            'image'      => $validatedData['image'] ?? 'default.jpg',
            'place_name' => $validatedData['place'],
            'start_date' => $validatedData['start_date'],
            'end_date'   => $validatedData['end_date'],
        ]);

        if ($vacation) {
            return response()->json(['message' => 'Trip successfully added!'], 201);
        } else {
            return response()->json(['message' => 'Error adding trip.'], 500);
        }
    }
}
