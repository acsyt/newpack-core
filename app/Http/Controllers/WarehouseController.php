<?php

namespace App\Http\Controllers;

use App\Http\Actions\Warehouse\CreateWarehouseAction;
use App\Http\Actions\Warehouse\UpdateWarehouseAction;
use App\Http\Requests\Warehouse\CreateWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Queries\WarehouseQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Warehouses")
 */
class WarehouseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/warehouses",
     *     summary="Get all warehouses",
     *     tags={"Warehouses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by name (partial match)"),
     *     @OA\Parameter(name="filter[type]", in="query", required=false, @OA\Schema(type="string"), description="Filter by type (exact match)"),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by active status (exact match)"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across multiple fields"),
     *     @OA\Response(response=200, description="Warehouses list", @OA\JsonContent(ref="#/components/schemas/WarehouseResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllWarehouses()
    {
        $warehouses = WarehouseQuery::make()->paginated();
        return WarehouseResource::collection($warehouses);
    }

    /**
     * @OA\Post(
     *     path="/api/warehouses",
     *     summary="Create a new warehouse",
     *     tags={"Warehouses"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateWarehouseRequest")),
     *     @OA\Response(response=201, description="Warehouse created", @OA\JsonContent(ref="#/components/schemas/WarehouseResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createWarehouse(CreateWarehouseRequest $request)
    {
        $data = $request->validated();
        $warehouse = app(CreateWarehouseAction::class)->handle($data);
        return response()->json([
            'data'      => new WarehouseResource($warehouse),
            'message'   => 'Warehouse created successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Get a warehouse by ID",
     *     tags={"Warehouses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="warehouse", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Warehouse details", @OA\JsonContent(ref="#/components/schemas/WarehouseResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneWarehouse($id)
    {
        $warehouse = WarehouseQuery::make()->findById((int) $id);
        return new WarehouseResource($warehouse);
    }

    /**
     * @OA\Patch(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Update a warehouse",
     *     tags={"Warehouses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="warehouse", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateWarehouseRequest")),
     *     @OA\Response(response=200, description="Warehouse updated", @OA\JsonContent(ref="#/components/schemas/WarehouseResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateWarehouse(UpdateWarehouseRequest $request, $id)
    {
        $warehouse = WarehouseQuery::make()->findById((int) $id);
        $data = $request->validated();
        $warehouse = app(UpdateWarehouseAction::class)->handle($warehouse, $data);
        return response()->json([
            'data'      => new WarehouseResource($warehouse),
            'message'   => 'Warehouse updated successfully',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Delete a warehouse",
     *     tags={"Warehouses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="warehouse", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteWarehouse($id)
    {
        $warehouse = WarehouseQuery::make()->findById((int) $id);
        $warehouse->delete();
        return response()->noContent();
    }
}
