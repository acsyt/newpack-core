<?php

namespace App\Http\Controllers;

use App\Http\Actions\Warehouse\CreateWarehouseLocationAction;
use App\Http\Actions\Warehouse\UpdateWarehouseLocationAction;
use App\Http\Requests\Warehouse\CreateWarehouseLocationRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseLocationRequest;
use App\Http\Resources\WarehouseLocationResource;
use App\Queries\WarehouseLocationQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Warehouse Locations")
 */
class WarehouseLocationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/warehouse-locations",
     *     summary="Get all warehouse locations",
     *     tags={"Warehouse Locations"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[warehouse_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by warehouse ID"),
     *     @OA\Parameter(name="filter[aisle]", in="query", required=false, @OA\Schema(type="string"), description="Filter by aisle"),
     *     @OA\Parameter(name="filter[shelf]", in="query", required=false, @OA\Schema(type="string"), description="Filter by shelf"),
     *     @OA\Parameter(name="filter[section]", in="query", required=false, @OA\Schema(type="string"), description="Filter by section"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across multiple fields"),
     *     @OA\Response(response=200, description="Locations list", @OA\JsonContent(ref="#/components/schemas/WarehouseLocationResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllLocations()
    {
        $locations = WarehouseLocationQuery::make()->paginated();
        return WarehouseLocationResource::collection($locations);
    }

    /**
     * @OA\Post(
     *     path="/api/warehouse-locations",
     *     summary="Create a new warehouse location",
     *     tags={"Warehouse Locations"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateWarehouseLocationRequest")),
     *     @OA\Response(response=201, description="Location created", @OA\JsonContent(ref="#/components/schemas/WarehouseLocationResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createLocation(CreateWarehouseLocationRequest $request)
    {
        $data = $request->validated();
        $location = app(CreateWarehouseLocationAction::class)->handle($data);
        return response()->json([
            'data'      => new WarehouseLocationResource($location),
            'message'   => 'Warehouse location created successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/warehouse-locations/{location}",
     *     summary="Get a warehouse location by ID",
     *     tags={"Warehouse Locations"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="location", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Location details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseLocationResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneLocation($id)
    {
        $location = WarehouseLocationQuery::make()->findById((int) $id);
        return response()->json(new WarehouseLocationResource($location));
    }

    /**
     * @OA\Patch(
     *     path="/api/warehouse-locations/{location}",
     *     summary="Update a warehouse location",
     *     tags={"Warehouse Locations"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="location", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateWarehouseLocationRequest")),
     *     @OA\Response(response=200, description="Location updated", @OA\JsonContent(ref="#/components/schemas/WarehouseLocationResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateLocation(UpdateWarehouseLocationRequest $request, $id)
    {
        $location = WarehouseLocationQuery::make()->findById((int) $id);
        $data = $request->validated();
        $location = app(UpdateWarehouseLocationAction::class)->handle($location, $data);
        return response()->json([
            'data'      => new WarehouseLocationResource($location),
            'message'   => 'Warehouse location updated successfully',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/warehouse-locations/{location}",
     *     summary="Delete a warehouse location",
     *     tags={"Warehouse Locations"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="location", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteLocation($id)
    {
        $location = WarehouseLocationQuery::make()->findById((int) $id);
        $location->delete();
        return response()->noContent();
    }
}
