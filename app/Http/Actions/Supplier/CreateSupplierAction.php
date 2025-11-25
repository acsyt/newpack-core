<?php

namespace App\Http\Actions\Supplier;

use App\Exceptions\CustomException;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CreateSupplierAction
{
    public function handle(array $data): Supplier
    {
        DB::beginTransaction();

        try {
            $data['created_by'] = Auth::id();

            $supplier = Supplier::create($data);

            DB::commit();

            return $supplier;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        }  catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to create supplier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'No se pudo crear el proveedor. Por favor, intente nuevamente.',
                500
            );
        }
    }
}
