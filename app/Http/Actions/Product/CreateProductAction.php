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

class CreateProductAction
{
    public function handle(array $data): Product
    {
        DB::beginTransaction();

        try {
            // Extract ingredients if present
            $ingredients = $data['ingredients'] ?? [];
            unset($data['ingredients']);

            // Set business logic defaults based on type
            // Note: $data['type'] will be a string from request, but we compare against Enum backing value
            if ($data['type'] === ProductType::RAW_MATERIAL->value) {
                $data['is_purchasable'] = $data['is_purchasable'] ?? true;
                $data['is_sellable'] = $data['is_sellable'] ?? false;
            } elseif ($data['type'] === ProductType::COMPOUND->value) {
                $data['is_purchasable'] = $data['is_purchasable'] ?? false;
                $data['is_sellable'] = $data['is_sellable'] ?? true;
            }

            // Create the product
            $product = Product::create($data);

            // If compound, attach ingredients
            if ($product->type === ProductType::COMPOUND && !empty($ingredients)) {
                $this->syncIngredients($product, $ingredients);
            }

            DB::commit();

            // Load relationships for response
            $product->load('ingredients');

            return $product;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error(CreateProductAction::class, [
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'data'      => $data,
                'user_id'   => Auth::id(),
            ]);

            throw new CustomException(
                'The system was unable to create the product. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Attach ingredients to a compound product
     */
    private function syncIngredients(Product $product, array $ingredients): void
    {
        $attachData = [];

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

            $attachData[$ingredientId] = [
                'quantity'          => $ingredient['quantity'],
                'wastage_percent'   => $ingredient['wastage_percent'] ?? 0,
                'process_stage'     => $ingredient['process_stage'] ?? null,
                'is_active'         => $ingredient['is_active'] ?? true,
            ];
        }

        $product->ingredients()->sync($attachData);
    }
}
