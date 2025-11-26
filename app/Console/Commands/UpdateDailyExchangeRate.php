<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Setting;
use App\Services\ExchangeRate\OpenExchangeRateService;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateDailyExchangeRate extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:daily-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el tipo de cambio de la moneda USD.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $exchangeRateService = app(OpenExchangeRateService::class);

        $currency = Currency::where('code', 'USD')
            ->where('active', 1)
            ->first();

        if (!$currency) {
            $message = 'Moneda USD no encontrada o inactiva.';
            Log::info($message);
            return $this->fail($message);
        }

        $exchangeRateValue = $exchangeRateService->getCurrentChangeRate();

        if (is_null($exchangeRateValue)) {
            $message = 'No se pudo obtener la tasa de cambio.';
            Log::info($message);
            return $this->fail($message);
        }

        $startDate = Carbon::now('UTC')->format('Y-m-d');
        $lastDate = Carbon::now('UTC')->subDay()->format('Y-m-d');

        ExchangeRate::where('start_date', $lastDate)
            ->orWhere('start_date', $startDate)
            ->where('currency_id', $currency->id)
            ->update(['active' => 0]);

        ExchangeRate::create([
            'currency_id'   => $currency->id,
            'value'         => $exchangeRateValue,
            'active'        => 1,
            'start_date'    => $startDate,
        ]);

        Setting::updateOrCreate(
            ['slug' => Setting::EXCHANGE_RATE],
            ['value' => $exchangeRateValue, 'description' => 'Tipo de cambio']
        );

        $message = 'Tipo de cambio actualizado correctamente.';
        Log::info($message);
        return $this->info($message);
    }
}
