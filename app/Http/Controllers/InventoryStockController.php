<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryStockResource;
use App\Queries\InventoryStockQuery;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Inventory Stocks')]
class InventoryStockController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/inventory/stocks",
     *     summary="List all inventory stocks",
     *     tags={"Inventory Stocks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[product_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by product ID"),
     *     @OA\Parameter(name="filter[warehouse_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by warehouse ID"),
     *     @OA\Parameter(name="filter[warehouse_location_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by warehouse location ID"),
     *     @OA\Parameter(name="filter[batch_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by batch ID"),
     *     @OA\Parameter(name="filter[status]", in="query", required=false, @OA\Schema(type="string", enum={"available", "reserved", "damaged"}), description="Filter by status"),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include related resources (product,warehouse,warehouseLocation,batch)"),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string"), description="Sort by field (prefix with - for descending)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of inventory stocks",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InventoryStockResource"))
     *         )
     *     )
     * )
     */
    public function findAllStocks()
    {
        $stocks = InventoryStockQuery::make()->paginated();
        return InventoryStockResource::collection($stocks);
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/stocks/{id}",
     *     summary="Get inventory stock by ID",
     *     tags={"Inventory Stocks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Stock ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock details",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryStockResource")
     *     ),
     *     @OA\Response(response=404, description="Stock not found")
     * )
     */
    public function findOneStock($id)
    {
        $stock = InventoryStockQuery::make()->findByIdOrFail($id);
        return new InventoryStockResource($stock);
    }
}
