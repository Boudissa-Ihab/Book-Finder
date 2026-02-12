<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class RegistrationController extends Controller
{
    #[OA\Post(
        path: '/register',
        summary: 'Register new users',
        description: 'Registration allow new users to login and access routes that need an authentication',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'In order to register, please provide all necessary data',
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ihab Boudissa'),
                    new OA\Property(property: 'email', type: 'string', description: 'Email should be unique', example: 'boudissa.ihab@gmail.com'),
                    new OA\Property(property: 'password', description: 'Password and password confirmation should match', type: 'string', example: '123456789'),
                    new OA\Property(property: 'password_confirmation', description: 'Password and password confirmation should match', type: 'string', example: '123456789'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 401, description: 'Trying to register while already being authenticated'),
            new OA\Response(response: 422, description: 'Registration errors'),
            new OA\Response(response: 500, description: 'Server error'),
        ],
        tags: ['Auth']
    )]
    public function __invoke(RegistrationRequest $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error("Registration error: \n" . $e->getMessage());
            return response()->json([
                'message' => 'Failed to register user. Please try again.',
            ], 500);
        }
    }
}
