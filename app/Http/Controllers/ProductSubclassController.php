<?php

namespace App\Http\Controllers;

use App\Http\Actions\ProductSubclass\CreateProductSubclassAction;
use App\Http\Actions\ProductSubclass\UpdateProductSubclassAction;
use App\Http\Resources\ProductSubclassResource;
use App\Http\Requests\ProductSubclass\StoreProductSubclassRequest;
use App\Http\Requests\ProductSubclass\UpdateProductSubclassRequest;
use App\Queries\ProductSubclassQuery;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Product Subclasses")
 */
class ProductSubclassController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/product-subclasses",
     *     summary="Get all product subclasses",
     *     tags={"Product Subclasses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[product_class_id]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include productClass relationship"),
     *     @OA\Response(response=200, description="Subclasses list", @OA\JsonContent(ref="#/components/schemas/ProductSubclassResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllProductSubclasses()
    {
        $subclasses = ProductSubclassQuery::make()->paginated();
        return ProductSubclassResource::collection($subclasses);
    }

    /**
     * @OA\Post(
     *     path="/api/product-subclasses",
     *     summary="Create a new product subclass",
     *     tags={"Product Subclasses"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreProductSubclassRequest")),
     *     @OA\Response(response=201, description="Subclass created", @OA\JsonContent(ref="#/components/schemas/ProductSubclassResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createProductSubclass(StoreProductSubclassRequest $request)
    {
        $data = $request->validated();
        $subclass = app(CreateProductSubclassAction::class)->handle($data);

        return response()->json([
            'data'      => new ProductSubclassResource($subclass),
            'message'   => 'Subclase de producto creada exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/product-subclasses/{productSubclass}",
     *     summary="Get a product subclass by ID",
     *     tags={"Product Subclasses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productSubclass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Subclass details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ProductSubclassResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneProductSubclass($id)
    {
        $subclass = ProductSubclassQuery::make()->findByIdOrFail((int) $id);
        return response()->json(new ProductSubclassResource($subclass));
    }

    /**
     * @OA\Patch(
     *     path="/api/product-subclasses/{productSubclass}",
     *     summary="Update a product subclass",
     *     tags={"Product Subclasses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productSubclass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateProductSubclassRequest")),
     *     @OA\Response(response=200, description="Subclass updated", @OA\JsonContent(ref="#/components/schemas/ProductSubclassResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProductSubclass(UpdateProductSubclassRequest $request, $id)
    {
        $subclass = ProductSubclassQuery::make()->findByIdOrFail((int) $id);
        $data = $request->validated();
        $subclass = app(UpdateProductSubclassAction::class)->handle($subclass, $data);

        return response()->json([
            'data'      => new ProductSubclassResource($subclass),
            'message'   => 'Subclase de producto actualizada exitosamente',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/product-subclasses/{productSubclass}",
     *     summary="Delete a product subclass",
     *     tags={"Product Subclasses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="productSubclass", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteProductSubclass($id)
    {
        $subclass = ProductSubclassQuery::make()->findByIdOrFail((int) $id);
        $subclass->delete();
        return response()->noContent();
    }
}
