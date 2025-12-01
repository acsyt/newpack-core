<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductTypeResource;
use App\Queries\ProductTypeQuery;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Product Types")
 */
class ProductTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/product-types",
     *     summary="Get all product types",
     *     tags={"Product Types"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string"), description="Filter by code (partial match)"),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by name (partial match)"),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string"), description="Sort by field (prefix with - for descending)"),
     *     @OA\Response(response=200, description="Product types list", @OA\JsonContent(ref="#/components/schemas/ProductTypeResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllProductTypes()
    {
        $productTypes = ProductTypeQuery::make()->paginated();
        return ProductTypeResource::collection($productTypes);
    }
}
