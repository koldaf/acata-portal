<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'currency',
        'title',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'updated_by');
    }
}
