<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['address'] ?? null,
        ]);

        $user['role'] = 'customer';
        $user->save();

        event(new Registered($user));

        return response()->json([
            'message' => "Registration successful",
            'user' => new UserResource($user),
        ], 201);
        // return new UserResource($user);
        // $validated = $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        //     'password' => ['required', 'confirmed', Password::defaults()],
        //     'role' => ['required', 'in:customer,restaurant,admin']
        // ]);

        // $user = User::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        //     'role' => $validated['role'],
        // ]);


        // event(new Registered($user));

        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'user' => $user,
        //     'token' => $token,
        // ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfull',
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required'
        // ]);

        // $user = User::where('email', $request->email)->first();

        // if (! $user || ! Hash::check($request->password, $user->password)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Invalid credentials'],
        //     ]);
        // }

        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'message' => "Login Successful",
        //     'user' => $user,
        //     'token' => $token,
        // ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();


        return response()->json([
            'message' => "Logged out successfully"
        ], 200);
    }
}
