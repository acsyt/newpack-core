<?php

namespace App\Http\Actions\Batch;

use App\Exceptions\CustomException;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateBatchAction
{
    /**
     * Create a new batch
     *
     * @param array $data
     * @return Batch
     */
    public function handle(array $data): Batch
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($data['product_id']);

            // Auto-generate batch_code if not provided
            if (empty($data['batch_code'])) {
                $data['batch_code'] = $this->generateBatchCode($product);
            }

            // Validate expiration date if product tracks batches
            if ($product->track_batches && !empty($data['expiration_date'])) {
                if (strtotime($data['expiration_date']) < time()) {
                    throw new CustomException('Expiration date cannot be in the past', 422);
                }
            }

            $batch = Batch::create([
                'product_id' => $product->id,
                'batch_code' => $data['batch_code'],
                'production_date' => $data['production_date'] ?? now(),
                'expiration_date' => $data['expiration_date'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'production_order_id' => $data['production_order_id'] ?? null,
                'initial_quantity' => $data['initial_quantity'],
                'current_quantity' => $data['initial_quantity'],
                'quality_certificate' => $data['quality_certificate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return $batch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch creation failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw new CustomException(
                'Failed to create batch: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Generate a unique batch code
     */
    private function generateBatchCode(Product $product): string
    {
        $prefix = 'BAT-' . strtoupper(substr($product->sku, 0, 6));
        $date = now()->format('Ymd');
        $sequence = Batch::whereDate('created_at', now())->count() + 1;

        return sprintf('%s-%s-%03d', $prefix, $date, $sequence);
    }
}
