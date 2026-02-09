<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function __invoke(RegistrationRequest $request)
    {
        $credentials = $request->validated();

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
        $user->assignRole(Roles::USER);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => [
                'value' => $token,
                'type' => 'Bearer',
            ]
        ], 201);
    }
}
