<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'clave', 'nombre', 'direccion', 'rfc', 'telefono', 'plazo', 'limite', 'saldo', 'zona_id'
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zona_id', 'id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'id');
    }
}
