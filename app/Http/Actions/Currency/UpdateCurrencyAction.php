<?php

namespace App\Http\Actions\Currency;

use App\Exceptions\CustomException;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class UpdateCurrencyAction
{
    public static function handle(Currency $currency, array $data) {

        DB::beginTransaction();
        try {
            $currency->update($data);

            DB::commit();
            return $currency->fresh();
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CustomException('Error updating currency', 500);
        }

    }
}
