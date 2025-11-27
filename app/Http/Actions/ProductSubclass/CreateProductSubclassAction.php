<?php

namespace App\Http\Actions\ProductSubclass;

use App\Exceptions\CustomException;
use App\Models\ProductSubclass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateProductSubclassAction
{
    public function handle(array $data): ProductSubclass
    {
        DB::beginTransaction();

        try {
            $subclass = ProductSubclass::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'product_class_id' => $data['product_class_id'],
                'description' => $data['description'] ?? null,
                'slug' => $data['slug'],
            ]);

            DB::commit();

            return $subclass->load('productClass');

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateProductSubclassAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo crear la subclase de producto. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
