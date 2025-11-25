<?php

namespace App\Http\Controllers;

use App\Http\Actions\Customer\CreateCustomerAction;
use App\Http\Actions\Customer\UpdateCustomerAction;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Queries\CustomerQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Customers")
 */
class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerQuery $customerQuery,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get all customers",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="client_type", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Customers list", @OA\JsonContent(ref="#/components/schemas/CustomerResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAll(Request $request)
    {
        $query = $this->customerQuery;
        $customers = $query->paginated($request);
        return CustomerResource::collection($customers);
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreCustomerRequest")),
     *     @OA\Response(response=201, description="Customer created", @OA\JsonContent(ref="#/components/schemas/CustomerResource")),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $customer = app(CreateCustomerAction::class)->handle($data);
        return response()->json(new CustomerResource($customer), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{customer}",
     *     summary="Get a customer by ID",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Customer details", @OA\JsonContent(ref="#/components/schemas/CustomerResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $customer = $this->customerQuery->findById((int) $id);
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        return new CustomerResource($customer);
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{customer}",
     *     summary="Update a customer",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateCustomerRequest")),
     *     @OA\Response(response=200, description="Customer updated", @OA\JsonContent(ref="#/components/schemas/CustomerResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $data = $request->validated();
        $customer = app(UpdateCustomerAction::class)->handle((int) $id, $data);
        return new CustomerResource($customer);
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{customer}",
     *     summary="Soft delete a customer",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy($id)
    {
        $customer = $this->customerQuery->findById((int) $id);
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        $customer->delete();
        return response()->noContent();
    }
}
