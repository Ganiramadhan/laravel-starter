<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for managing users"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users with search and pagination",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword for name or email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of records per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Users list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(property="role", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="first_page_url",
     *                     type="string",
     *                     example="http://localhost:8000/api/users?page=1"
     *                 ),
     *                 @OA\Property(
     *                     property="from",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="last_page",
     *                     type="integer",
     *                     example=5
     *                 ),
     *                 @OA\Property(
     *                     property="last_page_url",
     *                     type="string",
     *                     example="http://localhost:8000/api/users?page=5"
     *                 ),
     *                 @OA\Property(
     *                     property="next_page_url",
     *                     type="string",
     *                     example="http://localhost:8000/api/users?page=2",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     example="http://localhost:8000/api/users"
     *                 ),
     *                 @OA\Property(
     *                     property="per_page",
     *                     type="integer",
     *                     example=10
     *                 ),
     *                 @OA\Property(
     *                     property="prev_page_url",
     *                     type="string",
     *                     example=null,
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="to",
     *                     type="integer",
     *                     example=10
     *                 ),
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     example=50
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to fetch users")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = User::select('id', 'name', 'email', 'role');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('email', 'ILIKE', '%' . $request->search . '%');
                });
            }

            // Pagination
            $perPage = $request->query('per_page', 10);
            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users list retrieved successfully',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            Log::error('UserController@index: Failed to fetch users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="f82b3798-feed-4ec0-bfda-95cca1d48d37"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="1"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-10T10:30:20.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-10T10:30:20.000000Z"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to register user")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'id'       => (string) Str::uuid(),
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'admin',
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data'    => [
                    'user'  => $user,
                    'token' => $token,
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserController@register: Failed to register user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to register user'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="f82b3798-feed-4ec0-bfda-95cca1d48d37"),
     *                     @OA\Property(property="name", type="string", example="Dalimin Haryanto"),
     *                     @OA\Property(property="email", type="string", format="email", example="tedi98@example.net"),
     *                     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="role", type="string", example="3"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-10T10:30:20.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-10T10:30:20.000000Z"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to login")
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = JWTAuth::user();

            $responseData = [
                'user' => $user,
                'token' => $token,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $responseData
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserController@login: Failed to login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to login'
            ], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123", nullable=true),
     *             @OA\Property(property="old_password", type="string", example="password123", nullable=true),
     *             @OA\Property(property="role", type="string", example="1", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="role", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid old password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to update user")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|unique:users,email,' . $id,
                'password'     => 'nullable|string|min:6',
                'old_password' => 'required_with:password|string',
                'role'         => 'nullable|string',
            ]);

            if (!empty($validated['password'])) {
                if (empty($validated['old_password']) || !Hash::check($validated['old_password'], $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid old password'
                    ], 401);
                }
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            unset($validated['old_password']);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->only(['id', 'name', 'email', 'role'])
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('UserController@update: Failed to update user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete user")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('UserController@destroy: Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout a user",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or missing token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to logout")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing token'
            ], 401);
        } catch (\Exception $e) {
            Log::error('UserController@logout: Failed to logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}
