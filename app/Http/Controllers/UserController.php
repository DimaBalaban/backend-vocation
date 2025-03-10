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

public function getUsersWithVacations()
{
    $users = User::with('vacations')
                ->get()
                ->filter(function($user) {
                    return $user->vacations->isNotEmpty() &&
                           $user->vacations->first()->place_name &&
                           $user->vacations->first()->start_date &&
                           $user->vacations->first()->end_date;
                })
                ->map(function($user) {
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

        public function destroy($id){
        $user = User::find($id);

        if(!$user){
        return response()->json(['messege' => 'User not found'],404);
        }
        $user->delete();
        return response()->json(['messege' => 'User deleted successfully']);
        }
}
