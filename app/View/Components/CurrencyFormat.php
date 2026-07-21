<?php

namespace App\View\Components;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Componente Blade <x-currency-format>
 *
 * Delega toda la lógica al CurrencyService.
 *
 * Tipos disponibles:
 *   type="amount"     → símbolo + monto formateado  ("USD$ 15,420.00")
 *   type="conversion" → línea equivalente en MXN    ("Equivale a $ 285,270.00 MXN") | vacío si ya es MXN
 *   type="code"       → código entre paréntesis     ("(USD)")
 *
 * Uso:
 *   <x-currency-format :amount="$precio" :moneda-id="$monedaId" type="amount" />
 *   <x-currency-format :amount="$precio" :moneda-id="$monedaId" type="conversion" />
 *   <x-currency-format :moneda-id="$monedaId" type="code" />
 */
class CurrencyFormat extends Component
{
    public CurrencyService $currency;

    public function __construct(
        public float  $amount   = 0,
        public int    $monedaId = 1,
        public string $type     = 'amount'
    ) {
        $this->currency = CurrencyService::make($monedaId);
    }

    /** Devuelve el texto final según el tipo solicitado. */
    public function getOutput(): string
    {
        return match ($this->type) {
            'code'       => $this->currency->label(),
            'conversion' => $this->currency->conversion($this->amount),
            default      => $this->currency->format($this->amount),
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.currency-format');
    }
}
