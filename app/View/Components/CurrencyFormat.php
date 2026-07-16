<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CurrencyFormat extends Component
{
    public float $amount;
    public int $monedaId;
    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(float $amount = 0, int $monedaId = 1, string $type = 'amount')
    {
        $this->amount = $amount;
        $this->monedaId = $monedaId;
        $this->type = $type;
    }

    public function getOutput(): string
    {
        // Mock de monedas (SQLite no tiene tabla MONEDAS de Firebird)
        $monedas = [
            1 => 'MXN',
            2 => 'USD',
            3 => 'EUR',
        ];

        $code = $monedas[$this->monedaId] ?? 'MXN';

        if ($this->type === 'code') {
            return "($code)";
        }

        // Mock de tipo de cambio (para la fase actual)
        $tipoCambio = 18.50; 

        if ($this->type === 'conversion') {
            if ($code === 'USD') {
                $converted = $this->amount * $tipoCambio;
                return "Equivale a $ " . number_format($converted, 2) . " MXN";
            } else {
                $converted = $this->amount / $tipoCambio;
                return "Equivale a USD$ " . number_format($converted, 2);
            }
        }

        // Si es amount, ya no mostramos USD$ o Pesos, solo el formato numérico con $
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.currency-format');
    }
}
