<?php

namespace App\Http\Actions\Inventory;

use App\Exceptions\CustomException;
use App\Models\Batch;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessInventoryMovementAction
{
    /**
     * Process an inventory movement (entry or consumption)
     *
     * @param array $data [
     *   'product_id' => int,
     *   'type' => string (purchase_entry|production_output|production_consumption|sales_shipment|adjustment|transfer),
     *   'quantity' => float (positive for entry, negative for consumption),
     *   'batch_id' => int|null (for entries, specify existing batch or will auto-create),
     *   'batch_code' => string|null (for new batches),
     *   'reference_type' => string|null (PurchaseOrder, ProductionOrder, etc.),
     *   'reference_id' => int|null,
     *   'location' => string|null,
     *   'quality_certificate' => array|null (for purchase entries),
     * ]
     * @return InventoryTransaction
     */
    public function handle(array $data): InventoryTransaction
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($data['product_id']);
            $quantity = $data['quantity'];
            $type = $data['type'];

            // Determine if this is an entry (positive) or consumption (negative)
            $isEntry = in_array($type, ['purchase_entry', 'production_output', 'adjustment']) && $quantity > 0;

            $batch = null;

            if ($isEntry) {
                // ENTRY LOGIC: Create or use existing batch
                $batch = $this->handleEntry($data, $product);
            } else {
                // CONSUMPTION LOGIC: Use FIFO to consume from oldest batches
                $batch = $this->handleConsumption($data, $product);
            }

            // Calculate new balance
            $currentBalance = $this->getCurrentStock($product->id);
            $newBalance = $currentBalance + $quantity;

            // Create transaction record
            $transaction = InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'batch_id' => $batch?->id,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'location' => $data['location'] ?? null,
                'balance_after' => $newBalance,
            ]);

            // Update product's current_stock cache
            $product->update(['current_stock' => $newBalance]);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory movement failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw new CustomException(
                'Failed to process inventory movement: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Handle inventory entry (creates or updates batch)
     */
    private function handleEntry(array $data, Product $product): Batch
    {
        if (isset($data['batch_id'])) {
            // Use existing batch
            $batch = Batch::findOrFail($data['batch_id']);
            $batch->increment('current_quantity', $data['quantity']);
        } else {
            // Create new batch
            $batch = Batch::create([
                'product_id' => $product->id,
                'batch_code' => $data['batch_code'] ?? $this->generateBatchCode($product),
                'production_date' => $data['production_date'] ?? now(),
                'expiration_date' => $data['expiration_date'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'production_order_id' => $data['reference_type'] === 'ProductionOrder' ? $data['reference_id'] : null,
                'initial_quantity' => $data['quantity'],
                'current_quantity' => $data['quantity'],
                'quality_certificate' => $data['quality_certificate'] ?? null,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);
        }

        return $batch;
    }

    /**
     * Handle inventory consumption using FIFO logic
     */
    private function handleConsumption(array $data, Product $product): ?Batch
    {
        $quantityNeeded = abs($data['quantity']);

        // Get available batches ordered by expiration (FEFO) or production date (FIFO)
        $batches = Batch::where('product_id', $product->id)
            ->available()
            ->orderBy('expiration_date', 'asc')
            ->orderBy('production_date', 'asc')
            ->get();

        if ($batches->sum('current_quantity') < $quantityNeeded) {
            throw new CustomException(
                'Insufficient stock. Available: ' . $batches->sum('current_quantity') . ', Needed: ' . $quantityNeeded,
                400
            );
        }

        $lastBatchUsed = null;

        foreach ($batches as $batch) {
            if ($quantityNeeded <= 0) break;

            $quantityToConsume = min($batch->current_quantity, $quantityNeeded);

            $batch->decrement('current_quantity', $quantityToConsume);
            $quantityNeeded -= $quantityToConsume;

            if ($batch->current_quantity <= 0) {
                $batch->deplete();
            }

            $lastBatchUsed = $batch;
        }

        return $lastBatchUsed;
    }

    /**
     * Get current stock from latest transaction
     */
    private function getCurrentStock(int $productId): float
    {
        $latestTransaction = InventoryTransaction::where('product_id', $productId)
            ->latest()
            ->first();

        return $latestTransaction?->balance_after ?? 0;
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
