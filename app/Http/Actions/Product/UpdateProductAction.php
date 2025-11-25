<?php

namespace App\Http\Actions\Product;

use App\Exceptions\CustomException;
use App\Models\Product;
use App\Enums\ProductType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductAction
{
    public function handle(Product $product, array $data): Product
    {
        DB::beginTransaction();

        try {
            // Extract ingredients if present
            $ingredients = $data['ingredients'] ?? null;
            unset($data['ingredients']);

            // Update product attributes
            $product->update($data);

            // If compound and ingredients provided, sync them
            if ($product->type === ProductType::COMPOUND && $ingredients !== null) {
                $this->syncIngredients($product, $ingredients);
            }

            DB::commit();

            // Reload relationships for response
            $product->load('ingredients');

            return $product;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(UpdateProductAction::class, [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'data'          => $data,
                'product_id'    => $product->id,
                'user_id'       => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to update the product. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Sync ingredients for a compound product
     */
    private function syncIngredients(Product$product, array $ingredients): void
    {
        $syncData = [];

        foreach ($ingredients as $ingredient) {
            $ingredientId = $ingredient['ingredient_id'];

            // Validate: product cannot be its own ingredient
            if ($ingredientId == $product->id) {
                throw new CustomException(
                    'A product cannot be its own ingredient.',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Validate: ingredient exists
            $ingredientProduct = Product::find($ingredientId);
            if (!$ingredientProduct) {
                throw new CustomException(
                    "Ingredient with ID {$ingredientId} not found.",
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $syncData[$ingredientId] = [
                'quantity'          => $ingredient['quantity'],
                'wastage_percent'   => $ingredient['wastage_percent'] ?? 0,
                'process_stage'     => $ingredient['process_stage'] ?? null,
                'is_active'         => $ingredient['is_active'] ?? true,
            ];
        }

        $product->ingredients()->sync($syncData);
    }
}
