<?php

namespace App\Services;

/**
 * CurrencyService — Fuente única de verdad para conversión y formateo de moneda.
 *
 * Uso desde PHP:
 *   $svc = new CurrencyService(3720);
 *   $svc->format(15420.00);          // "USD$ 15,420.00"
 *   $svc->code();                     // "USD"
 *   $svc->symbol();                   // "USD$ "
 *   $svc->conversion(15420.00);       // "Equivale a $ 285,270.00 MXN"
 *   $svc->isUSD();                    // true
 *
 * Uso desde Blade (via helper global currency()):
 *   {{ currency($monedaId)->format($amount) }}
 *   {{ currency($monedaId)->code() }}
 *   {{ currency($monedaId)->conversion($amount) }}
 */
class CurrencyService
{
    // ── Catálogo de monedas ─────────────────────────────────────────────────
    // Clave: ID usado en el sistema (incluye IDs de Firebird ERP)
    // Valor: código ISO 4217
    private const CURRENCY_MAP = [
        1    => 'MXN',
        2    => 'USD',
        3    => 'EUR',
        3720 => 'USD',   // ID real de Firebird para dólares
    ];

    // Símbolo de presentación por código ISO
    private const SYMBOLS = [
        'MXN' => '$',
        'USD' => "USD$\u{00A0}",
        'EUR' => "EUR€\u{00A0}",
    ];

    // Tasas de cambio MOCK a MXN (se sustituirán con tabla de Firebird)
    private const RATES_TO_MXN = [
        'MXN' => 1.00,
        'USD' => 18.50,
        'EUR' => 19.98,
    ];

    private string $code;

    public function __construct(private int $monedaId = 1)
    {
        $this->code = self::CURRENCY_MAP[$monedaId] ?? 'MXN';
    }

    // ── Accesores básicos ───────────────────────────────────────────────────

    /** Código ISO: "MXN", "USD", "EUR" */
    public function code(): string
    {
        return $this->code;
    }

    /** Símbolo de presentación: "$", "USD$ ", "EUR€ " */
    public function symbol(): string
    {
        return self::SYMBOLS[$this->code] ?? '$';
    }

    /** true si la moneda activa es diferente a MXN */
    public function isForeign(): bool
    {
        return $this->code !== 'MXN';
    }

    /** Tipo de cambio respecto al MXN */
    public function rateToMxn(): float
    {
        return self::RATES_TO_MXN[$this->code] ?? 1.00;
    }

    // ── Formateo ────────────────────────────────────────────────────────────

    /**
     * Formatea un monto con el símbolo de la moneda activa.
     * Ej: format(15420) → "$15,420.00" (MXN) | "USD$ 15,420.00" (USD)
     */
    public function format(float $amount, int $decimals = 2): string
    {
        $sign = $amount < 0 ? '-' : '';
        return $sign . $this->symbol() . number_format(abs($amount), $decimals, '.', ',');
    }

    /**
     * Devuelve la línea de conversión a MXN, o cadena vacía si ya es MXN.
     * Ej: conversion(100) → "Equivale a $\u00a01,850.00 MXN" (USD)
     *                     → ""                              (MXN)
     */
    public function conversion(float $amount): string
    {
        if (!$this->isForeign()) {
            return '';
        }

        $mxn = $amount * $this->rateToMxn();
        return "Equivale a $\u{00A0}" . number_format($mxn, 2, '.', ',') . ' MXN';
    }

    /**
     * Devuelve solo el código entre paréntesis para etiquetas.
     * Ej: label() → "(USD)"
     */
    public function label(): string
    {
        return "({$this->code})";
    }

    // ── Factory estático ────────────────────────────────────────────────────

    /** Crea instancia desde un ID de moneda (permite encadenamiento). */
    public static function make(int $monedaId): static
    {
        return new static($monedaId);
    }
}
