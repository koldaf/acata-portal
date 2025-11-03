<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'event_name',
        'event_type',
        'event_date',
        'certificate_id',
        'status',
        'description',
    ];

    protected $casts = [
        'event_date' => 'date',
        'status' => 'string',
    ];

    /**
     * Relationship with member
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'member_id');
    }

    /**
     * Scope completed certificates
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Generate certificate ID
     */
    public static function generateCertificateId($memberId, $eventName): string
    {
        $prefix = 'ACATA-EVT';
        $eventCode = substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $eventName)), 0, 6);
        $memberCode = substr(strtoupper($memberId), -4);
        
        return "{$prefix}-{$eventCode}-{$memberCode}-" . time();
    }
}