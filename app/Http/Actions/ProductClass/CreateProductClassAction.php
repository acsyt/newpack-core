<?php

namespace App\Http\Actions\ProductClass;

use App\Exceptions\CustomException;
use App\Models\ProductClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateProductClassAction
{
    public function handle(array $data): ProductClass
    {
        DB::beginTransaction();

        try {
            $class = ProductClass::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'slug' => $data['slug'],
            ]);

            DB::commit();

            return $class;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateProductClassAction::class, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw new CustomException(
                'El sistema no pudo crear la clase de producto. Por favor, intente nuevamente.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
