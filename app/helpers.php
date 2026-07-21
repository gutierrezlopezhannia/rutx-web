<?php

use App\Services\CurrencyService;

if (! function_exists('currency')) {
    /**
     * Devuelve una instancia de CurrencyService lista para usar en Blade o PHP.
     *
     * Ejemplos de uso en Blade:
     *   {{ currency($monedaId)->format($amount) }}
     *   {{ currency($monedaId)->code() }}
     *   {{ currency($monedaId)->conversion($amount) }}
     *   @if(currency($monedaId)->isForeign()) ... @endif
     *
     * @param int $monedaId  ID de moneda (1=MXN, 3720=USD Firebird, etc.)
     */
    function currency(int $monedaId = 1): CurrencyService
    {
        return CurrencyService::make($monedaId);
    }
}
