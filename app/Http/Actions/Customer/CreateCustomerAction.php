<?php

namespace App\Http\Actions\Customer;

use App\Exceptions\CustomException;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CreateCustomerAction
{
    public function handle(array $data): Customer
    {
        DB::beginTransaction();

        try {
            $data['created_by'] = Auth::id();

            $customer = Customer::create($data);

            DB::commit();

            return $customer;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        }  catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to create customer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'No se pudo crear el cliente. Por favor, intente nuevamente.',
                500
            );
        }
    }
}
