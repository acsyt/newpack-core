<?php

namespace App\Http\Actions\Supplier;

use App\Exceptions\CustomException;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateSupplierAction
{
    public function handle(int $id, array $data): Supplier
    {
        DB::beginTransaction();

        try {
            $supplier = Supplier::findOrFail($id);

            $data['updated_by'] = Auth::id();

            $supplier->update($data);

            $supplier->refresh();

            DB::commit();

            return $supplier;

        } catch (CustomException $e) {
            DB::rollBack();

            throw $e;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to update supplier', [
                'supplier_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new CustomException(
                    'Supplier not found.',
                    404
                );
            }

            throw new CustomException(
                'No se pudo actualizar el proveedor. Por favor, intente nuevamente.',
                500
            );
        }
    }
}
