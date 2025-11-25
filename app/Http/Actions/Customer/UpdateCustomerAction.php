<?php

namespace App\Http\Actions\Customer;

use App\Exceptions\CustomException;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateCustomerAction
{
    public function handle(int $id, array $data): Customer
    {
        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($id);

            $originalData = $customer->only([
                'name', 'last_name', 'email', 'phone', 'status', 'client_type'
            ]);

            $data['updated_by'] = Auth::id();

            $customer->update($data);

            $customer->refresh();

            DB::commit();

            return $customer;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to update customer', [
                'customer_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new CustomException(
                    'Customer not found.',
                    404
                );
            }

            throw new CustomException(
                'No se pudo actualizar el cliente. Por favor, intente nuevamente.',
                500
            );
        }
    }
}
