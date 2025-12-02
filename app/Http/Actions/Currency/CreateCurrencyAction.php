<?php

namespace App\Http\Actions\Currency;

use App\Exceptions\CustomException;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class CreateCurrencyAction
{
    public static function handle(array $data) {
        DB::beginTransaction();
        try {

            $currency = Currency::create($data);

            DB::commit();

            return $currency;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            throw new CustomException('Error creating currency', 500);
        }
    }
}
