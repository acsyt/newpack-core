<?php

namespace App\Services\ExchangeRate;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;


class OpenExchangeRateService extends ExchangeRateService {

    const API_URL = 'https://openexchangerates.org/api';

    private $apiToken = '';

    public function __construct() {
        $this->apiToken = env('EXCHANGE_RATE_API_KEY') ?? '';
    }

    public function getCurrentChangeRate() {
        $url = self::API_URL . '/latest.json';

        $response = Http::get($url, [
            'app_id'    => $this->apiToken,
            'base'      => 'USD',
        ]);

        if( $response->failed() ) throw new CustomException('El token de acceso no es valido.');

        $data = $response->json();

        return $data['rates']['MXN'] ?? null;
    }
}

