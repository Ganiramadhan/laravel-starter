<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *     name="Shops",
 *     description="API for managing shops"
 * )
 */
class ShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/shops",
     *     summary="Retrieve a list of all shops",
     *     tags={"Shops"},
     *     @OA\Response(
     *         response=200,
     *         description="List of shops",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Shop"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Shop::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/shops",
     *     summary="Create a new shop",
     *     tags={"Shops"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Shop")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shop created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Shop")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create shop",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to create shop")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'logo_url' => 'nullable|string',
                'contact_whatsapp' => 'required|string|max:20',
                'description' => 'nullable|string',
                'subdomain' => 'required|string|unique:shops,subdomain'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $shop = Shop::create($request->all());

            return response()->json($shop, 201);
        } catch (\Exception $e) {
            Log::error('Failed to create shop', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shop'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/shops/{id}",
     *     summary="Retrieve the detail of a specific shop",
     *     tags={"Shops"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop detail",
     *         @OA\JsonContent(ref="#/components/schemas/Shop")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Shop not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        return response()->json($shop);
    }

    /**
     * @OA\Put(
     *     path="/api/shops/{id}",
     *     summary="Update an existing shop",
     *     tags={"Shops"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Shop")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop updated",
     *         @OA\JsonContent(ref="#/components/schemas/Shop")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Shop not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update shop",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to update shop")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shop not found'
                ], 404);
            }

            $shop->update($request->all());

            return response()->json($shop);
        } catch (\Exception $e) {
            Log::error('Failed to update shop', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shop'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/shops/{id}",
     *     summary="Delete a shop",
     *     tags={"Shops"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shop not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Shop not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete shop",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete shop")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $shop = Shop::findOrFail($id);
            $shop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shop deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('Shop not found', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete shop', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shop'
            ], 500);
        }
    }
}
