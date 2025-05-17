<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories;
use App\Repositories\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepository $auth)
    {
        $this->authRepository = $auth;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authRepository->register($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Registration successful',
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token,
            ]
        ], 201);
    
    }

    /**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Login user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="John Doe"),
 *                     @OA\Property(property="email", type="string", example="user@example.com"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 ),
 *                 @OA\Property(property="token", type="string", example="access_token_here")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="These credentials do not match our records."))
 *             )
 *         )
 *     )
 * )
 */
    public function login(Request $request)
    {
        $user = $this->authRepository->attemptLogin($request);

        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ]);
    
    }

    /**
 * @OA\Post(
 *     path="/api/auth/logout",
 *     summary="Logout user and revoke current token",
 *     tags={"Auth"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logout successful")
 *         )
 *     )
 * )
 */
    public function logout(Request $request)
    {
        $this->authRepository->logoutCurrentToken($request->user());

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
 * @OA\Get(
 *     path="/api/auth/me",
 *     tags={"Auth"},
 *     summary="Get authenticated user profile",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User profile retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User data retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-17T07:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-17T07:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
    public function me(Request $request)
    {
        $user = $this->authRepository->getUserById($request->user()->id);

        return response()->json([
            'message' => 'User data retrieved successfully',
            'data' => new UserResource($user),
        ]);
    }
}
