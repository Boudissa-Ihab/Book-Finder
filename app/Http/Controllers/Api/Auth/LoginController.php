<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: '/login',
        summary: 'Login users',
        description: 'Login users by providing an email and password',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'In order to login, users need to provide an email and a password',
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'boudissa.ihab@gmail.com'),
                    new OA\Property(property: 'password', type: 'string', example: '123456789'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Trying to login while already being authenticated'),
            new OA\Response(response: 422, description: 'Credentials validation errors'),
        ],
        tags: ['Auth']
    )]
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully',
                'token' => [
                    'value' => $token,
                    'type' => 'Bearer',
                ],
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**********************************/
    #[OA\Get(
        path: '/logout',
        summary: 'Logout user',
        description: 'Invalidate user\'s session',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 204, description: 'No Content'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 500, description: 'Failed to logout. Please try again.'),
        ],
        tags: ['Auth']
    )]
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully'], 204);
        } catch (\Exception $e) {
            Log::error("Logout error: \n" . $e->getMessage());
            return response()->json(['message' => 'Failed to logout. Please try again.'], 500);
        }
    }
}
