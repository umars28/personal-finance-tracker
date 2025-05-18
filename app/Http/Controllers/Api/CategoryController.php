<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
 * @OA\Get(
 *     path="/api/categories",
 *     summary="List all categories",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Fiction")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function index()
    {
        $categories = $this->categoryRepository->all();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data'    => CategoryResource::collection($categories),
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/categories",
 *     summary="Create new category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Fiction")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Category created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category created successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Fiction"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
 *             )
 *         )
 *     )
 * )
 */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryRepository->create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    /**
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     summary="Show single category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Fiction"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
 *             )
 *         )
 *     )
 * )
 */
    public function show($id)
    {
        $category = $this->categoryRepository->find($id);

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data'    => new CategoryResource($category),
        ]);
    }

    /**
 * @OA\Put(
 *     path="/api/categories/{id}",
 *     summary="Update category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Updated Category Name")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category updated successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Updated Category Name"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
 *             )
 *         )
 *     )
 * )
 */
    public function update(CategoryRequest $request, $id)
    {
        $category = $this->categoryRepository->update($id, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data'    => new CategoryResource($category),
        ]);
    }

    /**
 * @OA\Delete(
 *     path="/api/categories/{id}",
 *     summary="Delete category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category deleted successfully")
 *         )
 *     )
 * )
 */
    public function destroy($id)
    {
        $this->categoryRepository->delete($id);

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
