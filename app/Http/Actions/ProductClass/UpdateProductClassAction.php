<?php

namespace App\Http\Actions\ProductClass;

use App\Exceptions\CustomException;
use App\Models\ProductClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductClassAction
{
    public function handle(ProductClass $class, array $data): ProductClass
    {
        DB::beginTransaction();

        try {
            $class->update($data);

            DB::commit();

            return $class->refresh();

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(UpdateProductClassAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'class_id' => $class->id,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo actualizar la clase de producto. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
