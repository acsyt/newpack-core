<?php

namespace App\Http\Actions\Product;

use App\Models\Product;
use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class SyncProductIngredientsAction
{
    public function handle(Product $product, array $ingredients): void
    {
        if (empty($ingredients)) {
            $product->ingredients()->sync([]);
            return;
        }

        $ingredientIds = array_column($ingredients, 'ingredient_id');

        if (in_array($product->id, $ingredientIds)) {
            throw new CustomException(
                'A product cannot be its own ingredient.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $existingCount = Product::whereIn('id', $ingredientIds)->count();

        if ($existingCount !== count(array_unique($ingredientIds))) {
            throw new CustomException(
                'One or more ingredients provided do not exist.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $attachData = [];
        foreach ($ingredients as $ingredient) {
            $attachData[$ingredient['ingredient_id']] = [
                'quantity'          => $ingredient['quantity'],
                'wastage_percent'   => $ingredient['wastage_percent'] ?? 0,
                'process_stage'     => $ingredient['process_stage'] ?? null,
                'is_active'         => $ingredient['is_active'] ?? true,
            ];
        }

        $product->ingredients()->sync($attachData);
    }
}
