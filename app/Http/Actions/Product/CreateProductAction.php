<?php

namespace App\Http\Actions\Product;

use App\Enums\ProductType;
use App\Exceptions\CustomException;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CreateProductAction
{
    public function __construct(
        protected SyncProductIngredientsAction $syncIngredients
    ) {}

    public function handle(array $data): Product
    {
        DB::beginTransaction();

        try {
            $ingredients = $data['ingredients'] ?? [];
            unset($data['ingredients']);

            $product = Product::create($data);

            DB::commit();
            $product->load('ingredients');

            return $product;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating product', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new CustomException(
                'Error creating product.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
