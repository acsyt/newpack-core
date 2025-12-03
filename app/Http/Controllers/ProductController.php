<?php

namespace App\Http\Controllers;

use App\Http\Actions\Product\CreateProductAction;
use App\Http\Actions\Product\UpdateProductAction;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Queries\ProductQuery;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Products")
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by product name (partial match)"),
     *     @OA\Parameter(name="filter[sku]", in="query", required=false, @OA\Schema(type="string"), description="Filter by SKU (partial match)"),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, @OA\Schema(type="string"), description="Filter by type (exact match): raw_material, compound, ingredient, service, wip"),
     *     @OA\Parameter(name="filter[is_active]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by active status"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across multiple fields using the search scope"),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include relationships: ingredients, usedInCompounds"),
     *     @OA\Response(response=200, description="Products list", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllProducts()
    {
        $products = ProductQuery::make()->paginated();
        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")),
     *     @OA\Response(response=201, description="Product created", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createProduct(StoreProductRequest $request, CreateProductAction $createProductAction)
    {
        $data = $request->validated();
        $product = $createProductAction->handle($data);

        return response()->json([
            'data'      => new ProductResource($product),
            'message'   => 'Registro creado exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     summary="Get a product by ID",
     *     tags={"Products"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include relationships: ingredients, usedInCompounds"),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneProduct($id)
    {
        $product = ProductQuery::make()->findById((int) $id);
        return response()->json(new ProductResource($product));
    }

    /**
     * @OA\Put(
     *     path="/api/products/{product}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")),
     *     @OA\Response(response=200, description="Product updated", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProduct(UpdateProductRequest $request, Product $product, UpdateProductAction $updateProductAction)
    {
        $data = $request->validated();
        $product = $updateProductAction->handle($product, $data);

        return response()->json([
            'data'      => new ProductResource($product),
            'message'   => 'Registro actualizado exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}",
     *     summary="Soft delete a product",
     *     tags={"Products"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteProduct($id)
    {
        $product = ProductQuery::make()->findById((int) $id);
        $product->delete();
        return response()->noContent();
    }
}
