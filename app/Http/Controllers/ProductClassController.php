<?php

namespace App\Http\Controllers;

use App\Http\Actions\ProductClass\CreateProductClassAction;
use App\Http\Actions\ProductClass\UpdateProductClassAction;
use App\Http\Resources\ProductClassResource;
use App\Http\Requests\ProductClass\StoreProductClassRequest;
use App\Http\Requests\ProductClass\UpdateProductClassRequest;
use App\Queries\ProductClassQuery;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Product Classes")
 */
class ProductClassController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/product-classes",
     *     summary="Get all product classes",
     *     tags={"Product Classes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Classes list", @OA\JsonContent(ref="#/components/schemas/ProductClassResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllProductClasses()
    {
        $classes = ProductClassQuery::make()->paginated();
        return ProductClassResource::collection($classes);
    }

    /**
     * @OA\Post(
     *     path="/api/product-classes",
     *     summary="Create a new product class",
     *     tags={"Product Classes"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreProductClassRequest")),
     *     @OA\Response(response=201, description="Class created", @OA\JsonContent(ref="#/components/schemas/ProductClassResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createProductClass(StoreProductClassRequest $request)
    {
        $data = $request->validated();
        $class = app(CreateProductClassAction::class)->handle($data);

        return response()->json([
            'data'      => new ProductClassResource($class),
            'message'   => 'Clase de producto creada exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/product-classes/{productClass}",
     *     summary="Get a product class by ID",
     *     tags={"Product Classes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productClass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Class details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ProductClassResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneProductClass($id)
    {
        $class = ProductClassQuery::make()->findByIdOrFail((int) $id);
        return response()->json(new ProductClassResource($class));
    }

    /**
     * @OA\Patch(
     *     path="/api/product-classes/{productClass}",
     *     summary="Update a product class",
     *     tags={"Product Classes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productClass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateProductClassRequest")),
     *     @OA\Response(response=200, description="Class updated", @OA\JsonContent(ref="#/components/schemas/ProductClassResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProductClass(UpdateProductClassRequest $request, $id)
    {
        $class = ProductClassQuery::make()->findByIdOrFail((int) $id);
        $data = $request->validated();
        $class = app(UpdateProductClassAction::class)->handle($class, $data);

        return response()->json([
            'data'      => new ProductClassResource($class),
            'message'   => 'Clase de producto actualizada exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/product-classes/{productClass}",
     *     summary="Delete a product class",
     *     tags={"Product Classes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productClass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteProductClass($id)
    {
        $class = ProductClassQuery::make()->findByIdOrFail((int) $id);
        $class->delete();
        return response()->noContent();
    }
}
