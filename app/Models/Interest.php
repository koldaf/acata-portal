<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'interest',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationship with members
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Members::class,
            'membership_interests',
            'interest_id',
            'member_id'
        );
    }

    /**
     * Scope active interests
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if interest is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate interest
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate interest
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }
}