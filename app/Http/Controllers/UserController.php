<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

  public function register(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:users',
          'password' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
          return response()->json(['errors' => $validator->errors()], 422);
      }

      try {
          $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'password' => Hash::make($request->password),
              'role' => 'user',
          ]);
      } catch (\Exception $e) {
          return response()->json(['error' => 'User creation failed', 'message' => $e->getMessage()], 500);
      }

      auth()->login($user);

      return response()->json([
          'message' => 'User registered successfully',
          'user' => $user,
      ], 201);
  }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'country' => 'nullable|string|max:255',
        'entryDate' => 'nullable|date',
        'exitDate' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => '',
        ]);

        if (!$user) {
            return response()->json(['error' => 'Failed to create user'], 500);
        }


        if ($request->country || $request->entryDate || $request->exitDate) {
            $vacation = $user->vacations()->create([
                'place_name' => $request->country,
                'start_date' => $request->entryDate,
                'end_date' => $request->exitDate,
            ]);

            if (!$vacation) {
                return response()->json(['error' => 'Failed to create vacation data'], 500);
            }
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user->load('vacations')], 201);
    } catch (\Exception $e) {
        \Log::error('User creation failed: ' . $e->getMessage());
        return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
    }
}

public function getUsersWithVacations(Request $request)
{
    $search = $request->query('search');

    $users = User::with('vacations')
        ->when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
        ->get()
        ->filter(function ($user) {
            return $user->vacations->isNotEmpty() &&
                   $user->vacations->first()->place_name &&
                   $user->vacations->first()->start_date &&
                   $user->vacations->first()->end_date;
        })
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'country' => $user->vacations->first()->place_name ?? 'No country',
                'entryDate' => $user->vacations->first()->start_date ?? 'No entry date',
                'exitDate' => $user->vacations->first()->end_date ?? 'No exit date',
            ];
        });

    return response()->json($users);
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

        public function update(Request $request, $id)
    {
       $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'country' => 'nullable|string|max:255',
        'entryDate' => 'nullable|date',
        'exitDate' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

       $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'country' => $request->country,
        'entryDate' => $request->entryDate,
        'exitDate' => $request->exitDate,
    ]);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user,
    ]);
}

public function updateVacation(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'country' => 'nullable|string|max:255',
        'entryDate' => 'nullable|date',
        'exitDate' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user->vacations()->update([
        'place_name' => $request->country,
        'start_date' => $request->entryDate,
        'end_date' => $request->exitDate,
    ]);

    return response()->json([
        'message' => 'Vacation data updated successfully',
        'user' => $user->load('vacations'),
    ]);
}


        public function destroy($id){
        $user = User::find($id);

        if(!$user){
        return response()->json(['messege' => 'User not found'],404);
        }
        $user->delete();
        return response()->json(['messege' => 'User deleted successfully']);
        }

}

