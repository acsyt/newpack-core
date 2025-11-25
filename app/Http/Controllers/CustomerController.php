<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Actions\Customer\CreateCustomerAction;
use App\Http\Actions\Customer\UpdateCustomerAction;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Queries\CustomerQuery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Customers")
 */
class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get all customers",
     *     tags={"Customers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by customer name (partial match)"),
     *     @OA\Parameter(name="filter[last_name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by customer last name (partial match)"),
     *     @OA\Parameter(name="filter[email]", in="query", required=false, @OA\Schema(type="string"), description="Filter by customer email (partial match)"),
     *     @OA\Parameter(name="filter[rfc]", in="query", required=false, @OA\Schema(type="string"), description="Filter by customer RFC (partial match)"),
     *     @OA\Parameter(name="filter[status]", in="query", required=false, @OA\Schema(type="string"), description="Filter by status (exact match)"),
     *     @OA\Parameter(name="filter[client_type]", in="query", required=false, @OA\Schema(type="string"), description="Filter by client type (exact match)"),
     *     @OA\Parameter(name="filter[suburb_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by suburb ID (exact match)"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search across multiple fields using the search scope"),
     *     @OA\Response(response=200, description="Customers list", @OA\JsonContent(ref="#/components/schemas/CustomerResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function findAllCustomers()
    {
        $users = CustomerQuery::make()->paginated();
        return CustomerResource::collection($users);
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
    public function createCustomer(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $customer = app(CreateCustomerAction::class)->handle($data);
        return response()->json([
            'data'      => new CustomerResource($customer),
            'message'   => 'Customer created successfully',
        ], Response::HTTP_CREATED);
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
    public function findOneCustomer($id)
    {
        $customer = CustomerQuery::make()->findById((int) $id);
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
    public function updateCustomer(UpdateCustomerRequest $request, $id)
    {
        $customer = CustomerQuery::make()->findById((int) $id);
        $data = $request->validated();
        $customer = app(UpdateCustomerAction::class)->handle($customer, $data);
        return response()->json([
            'data'      => new CustomerResource($customer),
            'message'   => 'Customer updated successfully',
        ], Response::HTTP_OK);
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
    public function deleteCustomer($id)
    {
        $customer = CustomerQuery::make()->findById((int) $id);
        $customer->delete();
        return response()->noContent();
    }
}
