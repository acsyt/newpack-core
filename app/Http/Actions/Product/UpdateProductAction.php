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

class UpdateProductAction
{
    public function __construct(
        protected SyncProductIngredientsAction $syncIngredients
    ) {}

    public function handle(Product $product, array $data): Product
    {
        DB::beginTransaction();

        try {
            $ingredients = $data['ingredients'] ?? null;
            unset($data['ingredients']);

            $product->update($data);

            if ($ingredients !== null) {
                if ($product->type === ProductType::COMPOUND) {
                    $this->syncIngredients->handle($product, $ingredients);
                } elseif (!empty($ingredients)) {
                    throw new CustomException(
                        'Raw materials cannot have ingredients.',
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                } else {
                    $product->ingredients()->detach();
                }
            }

            DB::commit();
            $product->load('ingredients');

            return $product;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating product', [
                'product_id' => $product->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new CustomException(
                'Error updating product.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
