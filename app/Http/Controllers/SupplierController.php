<?php

namespace App\Http\Controllers;

use App\Http\Actions\Supplier\CreateSupplierAction;
use App\Http\Actions\Supplier\UpdateSupplierAction;
use App\Http\Resources\SupplierResource;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Queries\SupplierQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Suppliers")
 */
class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierQuery $supplierQuery,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/suppliers",
     *     summary="Get all suppliers",
     *     tags={"Suppliers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[company_name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by company name (partial match)"),
     *     @OA\Parameter(name="filter[contact_name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by contact name (partial match)"),
     *     @OA\Parameter(name="filter[email]", in="query", required=false, @OA\Schema(type="string"), description="Filter by email (partial match)"),
     *     @OA\Parameter(name="filter[rfc]", in="query", required=false, @OA\Schema(type="string"), description="Filter by RFC (partial match)"),
     *     @OA\Parameter(name="filter[status]", in="query", required=false, @OA\Schema(type="string"), description="Filter by status (exact match)"),
     *     @OA\Parameter(name="filter[supplier_type]", in="query", required=false, @OA\Schema(type="string"), description="Filter by supplier type (exact match)"),
     *     @OA\Parameter(name="filter[suburb_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by suburb ID (exact match)"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across multiple fields using the search scope"),
     *     @OA\Response(response=200, description="Suppliers list", @OA\JsonContent(ref="#/components/schemas/SupplierResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllSuppliers(Request $request)
    {
        $suppliers = SupplierQuery::make()->paginated();
        return SupplierResource::collection($suppliers);
    }

    /**
     * @OA\Post(
     *     path="/api/suppliers",
     *     summary="Create a new supplier",
     *     tags={"Suppliers"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreSupplierRequest")),
     *     @OA\Response(response=201, description="Supplier created", @OA\JsonContent(ref="#/components/schemas/SupplierResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createSupplier(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        $supplier = app(CreateSupplierAction::class)->handle($data);
        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'El proveedor se ha creado exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/suppliers/{supplier}",
     *     summary="Get a supplier by ID",
     *     tags={"Suppliers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Supplier details", @OA\JsonContent(ref="#/components/schemas/SupplierResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findOneSupplier($id)
    {
        $supplier = SupplierQuery::make()->findById((int) $id);
        return new SupplierResource($supplier);
    }

    /**
     * @OA\Put(
     *     path="/api/suppliers/{supplier}",
     *     summary="Update a supplier",
     *     tags={"Suppliers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateSupplierRequest")),
     *     @OA\Response(response=200, description="Supplier updated", @OA\JsonContent(ref="#/components/schemas/SupplierResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateSupplier(UpdateSupplierRequest $request, $id)
    {
        $data = $request->validated();
        $supplier = SupplierQuery::make()->findById((int) $id);
        $supplier->update($data);
        return response()->json([
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier updated successfully',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/suppliers/{supplier}",
     *     summary="Soft delete a supplier",
     *     tags={"Suppliers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteSupplier($id)
    {
        $supplier = SupplierQuery::make()->findById((int) $id);
        $supplier->delete();
        return response()->noContent();
    }
}
