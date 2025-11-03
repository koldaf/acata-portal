<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'certificate_type',
        'certificate_id',
        'event_name',
        'ip_address',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    /**
     * Relationship with member
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'member_id');
    }

    /**
     * Scope by certificate type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }

    /**
     * Scope recent downloads
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('downloaded_at', '>=', now()->subDays($days));
    }
}