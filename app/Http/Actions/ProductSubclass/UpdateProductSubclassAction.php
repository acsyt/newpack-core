<?php

namespace App\Http\Actions\ProductSubclass;

use App\Exceptions\CustomException;
use App\Models\ProductSubclass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductSubclassAction
{
    public function handle(ProductSubclass $subclass, array $data): ProductSubclass
    {
        DB::beginTransaction();

        try {
            $subclass->update($data);

            DB::commit();

            return $subclass->load('productClass')->refresh();

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(UpdateProductSubclassAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'subclass_id' => $subclass->id,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo actualizar la subclase de producto. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
