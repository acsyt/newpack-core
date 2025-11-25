<?php

namespace App\Http\Actions\Customer;

use App\Exceptions\CustomException;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateCustomerAction
{
    public function handle(Customer $customer, array $data): Customer
    {
        DB::beginTransaction();

        try {
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

            Log::error(UpdateCustomerAction::class, [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'data'        => $data,
                'user_id'     => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to update the customer. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
