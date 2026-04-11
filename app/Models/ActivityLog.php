<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'action',
        'http_method',
        'route_name',
        'path',
        'response_status',
        'ip_address',
        'user_agent',
        'payload',
        'performed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'performed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'member_id');
    }
}
