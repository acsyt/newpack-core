<?php

namespace App\Http\Actions\Product;

use App\Exceptions\CustomException;
use App\Enums\ProductType;
use App\Models\Product;
use App\Models\ProductType as ModelsProductType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            Log::info('Ingredients', ['ingredients' => $ingredients]);
            unset($data['ingredients']);

            $productType = ModelsProductType::where('code', $data['type'])->first();
            
            if (!$productType) {
                throw new CustomException(
                    'El tipo de producto no existe.',
                    Response::HTTP_NOT_FOUND
                );
            }

            $data['product_type_id'] = $productType->id;
            $data['slug'] = Str::slug($data['name']);

            $product = Product::create($data);

            if ($productType->code === ProductType::COMPOUND->value && !empty($ingredients)) {
                $this->syncIngredients->handle($product, $ingredients);
            }

            $product->load(['ingredients.productType', 'ingredients.measureUnit']);
            
            DB::commit();
            
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
