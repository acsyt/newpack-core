<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierBankAccount;
use App\Http\Resources\SupplierBankAccountResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Supplier Bank Accounts")
 */
class SupplierBankAccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/suppliers/{supplier}/bank-accounts",
     *     summary="Get all bank accounts for a supplier",
     *     tags={"Supplier Bank Accounts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Bank accounts list", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SupplierBankAccountResource"))),
     *     @OA\Response(response=404, description="Supplier not found")
     * )
     */
    public function findAll($supplierId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], Response::HTTP_NOT_FOUND);
        }

        $bankAccounts = $supplier->bankAccounts;
        return SupplierBankAccountResource::collection($bankAccounts);
    }

    /**
     * @OA\Post(
     *     path="/api/suppliers/{supplier}/bank-accounts",
     *     summary="Create a bank account for a supplier",
     *     tags={"Supplier Bank Accounts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"bank_name"},
     *         @OA\Property(property="bank_name", type="string", example="BBVA"),
     *         @OA\Property(property="account_number", type="string", nullable=true, example="0123456789"),
     *         @OA\Property(property="clabe", type="string", nullable=true, example="012180001234567890"),
     *         @OA\Property(property="swift_code", type="string", nullable=true, example="BBVAMXMMXXX"),
     *         @OA\Property(property="currency", type="string", example="MXN"),
     *         @OA\Property(property="is_primary", type="boolean", example=false),
     *         @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *         @OA\Property(property="notes", type="string", nullable=true, example="Cuenta principal")
     *     )),
     *     @OA\Response(response=201, description="Bank account created", @OA\JsonContent(ref="#/components/schemas/SupplierBankAccountResource")),
     *     @OA\Response(response=404, description="Supplier not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createBankAccount($supplierId, Request $request)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'clabe' => 'nullable|string|size:18|unique:supplier_bank_accounts,clabe',
            'swift_code' => 'nullable|string|max:11',
            'currency' => 'nullable|string|size:3',
            'is_primary' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['supplier_id'] = $supplierId;

        $bankAccount = SupplierBankAccount::create($validated);

        return response()->json(new SupplierBankAccountResource($bankAccount), Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/suppliers/{supplier}/bank-accounts/{bankAccount}",
     *     summary="Update a bank account",
     *     tags={"Supplier Bank Accounts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="bankAccount", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="bank_name", type="string", example="BBVA"),
     *         @OA\Property(property="account_number", type="string", nullable=true, example="0123456789"),
     *         @OA\Property(property="clabe", type="string", nullable=true, example="012180001234567890"),
     *         @OA\Property(property="swift_code", type="string", nullable=true, example="BBVAMXMMXXX"),
     *         @OA\Property(property="currency", type="string", example="MXN"),
     *         @OA\Property(property="is_primary", type="boolean", example=false),
     *         @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *         @OA\Property(property="notes", type="string", nullable=true, example="Cuenta actualizada")
     *     )),
     *     @OA\Response(response=200, description="Bank account updated", @OA\JsonContent(ref="#/components/schemas/SupplierBankAccountResource")),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateBankAccount($supplierId, $bankAccountId, Request $request)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], Response::HTTP_NOT_FOUND);
        }

        $bankAccount = SupplierBankAccount::where('supplier_id', $supplierId)
            ->where('id', $bankAccountId)
            ->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'bank_name' => 'sometimes|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'clabe' => 'nullable|string|size:18|unique:supplier_bank_accounts,clabe,' . $bankAccountId,
            'swift_code' => 'nullable|string|max:11',
            'currency' => 'nullable|string|size:3',
            'is_primary' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $bankAccount->update($validated);

        return new SupplierBankAccountResource($bankAccount);
    }

    /**
     * @OA\Delete(
     *     path="/api/suppliers/{supplier}/bank-accounts/{bankAccount}",
     *     summary="Delete a bank account",
     *     tags={"Supplier Bank Accounts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="bankAccount", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function deleteBankAccount($supplierId, $bankAccountId)
    {
        $bankAccount = SupplierBankAccount::where('supplier_id', $supplierId)
            ->where('id', $bankAccountId)
            ->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], Response::HTTP_NOT_FOUND);
        }

        $bankAccount->delete();
        return response()->noContent();
    }

    /**
     * @OA\Post(
     *     path="/api/suppliers/{supplier}/bank-accounts/{bankAccount}/set-primary",
     *     summary="Set a bank account as primary",
     *     tags={"Supplier Bank Accounts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="supplier", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="bankAccount", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Bank account set as primary", @OA\JsonContent(ref="#/components/schemas/SupplierBankAccountResource")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function setPrimary($supplierId, $bankAccountId)
    {
        $bankAccount = SupplierBankAccount::where('supplier_id', $supplierId)
            ->where('id', $bankAccountId)
            ->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], Response::HTTP_NOT_FOUND);
        }

        $bankAccount->update(['is_primary' => true]);

        return new SupplierBankAccountResource($bankAccount);
    }
}
