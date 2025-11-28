<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryMovementResource;
use App\Queries\InventoryMovementQuery;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Inventory Movements')]
class InventoryMovementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/inventory/movements",
     *     summary="List all inventory movements (Kardex)",
     *     tags={"Inventory Movements"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[product_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by product ID"),
     *     @OA\Parameter(name="filter[warehouse_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by warehouse ID"),
     *     @OA\Parameter(name="filter[warehouse_location_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by warehouse location ID"),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, @OA\Schema(type="string", enum={"purchase_entry", "production_output", "production_consumption", "sales_shipment", "adjustment", "transfer"}), description="Filter by movement type"),
     *     @OA\Parameter(name="filter[user_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by user ID"),
     *     @OA\Parameter(name="filter[created_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include related resources (product,warehouse,warehouseLocation,batch,user,reference)"),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string"), description="Sort by field (prefix with - for descending)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of inventory movements",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InventoryMovementResource"))
     *         )
     *     )
     * )
     */
    public function findAllMovements()
    {
        $movements = InventoryMovementQuery::make()->paginated();
        return InventoryMovementResource::collection($movements);
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/movements/{id}",
     *     summary="Get inventory movement by ID",
     *     tags={"Inventory Movements"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movement ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movement details",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryMovementResource")
     *     ),
     *     @OA\Response(response=404, description="Movement not found")
     * )
     */
    public function findOneMovement($id)
    {
        $movement = InventoryMovementQuery::make()->findByIdOrFail($id);
        return new InventoryMovementResource($movement);
    }
    /**
     * @OA\Post(
     *     path="/api/inventory/movements",
     *     summary="Create a new inventory movement (Entry/Exit)",
     *     tags={"Inventory Movements"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"warehouse_id", "product_id", "type", "quantity"},
     *             @OA\Property(property="warehouse_id", type="integer"),
     *             @OA\Property(property="warehouse_location_id", type="integer", nullable=true),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="type", type="string", enum={"entry", "exit"}),
     *             @OA\Property(property="quantity", type="number"),
     *             @OA\Property(property="batch_id", type="integer", nullable=true),
     *             @OA\Property(property="notes", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movement created",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryMovementResource")
     *     )
     * )
     */
    public function store(\App\Http\Requests\Inventory\StoreInventoryMovementRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = \Illuminate\Support\Facades\Auth::id();
        $data['balance_after'] = 0; // Placeholder

        $movement = \App\Models\InventoryMovement::create($data);

        return new InventoryMovementResource($movement);
    }

    /**
     * @OA\Post(
     *     path="/api/inventory/movements/transfer",
     *     summary="Create a new inventory transfer",
     *     tags={"Inventory Movements"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"source_warehouse_id", "destination_warehouse_id", "products"},
     *             @OA\Property(property="source_warehouse_id", type="integer"),
     *             @OA\Property(property="destination_warehouse_id", type="integer"),
     *             @OA\Property(property="notes", type="string", nullable=true),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="number"),
     *                     @OA\Property(property="source_location_id", type="integer", nullable=true),
     *                     @OA\Property(property="destination_location_id", type="integer", nullable=true),
     *                     @OA\Property(property="batch_id", type="integer", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transfer created successfully"
     *     )
     * )
     */
    public function transfer(\App\Http\Requests\Inventory\StoreInventoryTransferRequest $request, \App\Actions\Inventory\CreateInventoryTransferAction $action)
    {
        $action->execute($request->validated());

        return response()->json(['message' => 'Transfer created successfully'], 201);
    }
}
