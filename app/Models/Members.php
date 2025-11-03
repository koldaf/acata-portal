<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Interest;
use App\Models\CertificateDownload;
use App\Models\EventCertificate;
use Illuminate\Database\Eloquent\Relations\HasMany as RelationsHasMany;

class Members extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'membership_type',
        'member_id',
        'phone',
        'affiliation',
        'job_title',
        'country',
        'status',
        'bio',
        'profile_picture',
        'social_links',
        'created_on',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified' => 'string',
        'status' => 'string',
        'created_on' => 'date',
        'social_links' => 'array',
    ];

    /**
     * Boot function for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique member_id before creating
        static::creating(function ($member) {
            if (empty($member->member_id)) {
                $member->member_id = static::generateMemberId();
            }
        });

        // Hash password before saving
        static::saving(function ($member) {
            if ($member->isDirty('password')) {
                $member->password = Hash::make($member->password);
            }
        });
    }

    /**
     * Generate unique member ID
     */
    protected static function generateMemberId(): string
    {
        $prefix = 'ACATA';
        $year = date('Y');
        
        do {
            $random = strtoupper(substr(uniqid(), -6));
            $memberId = "{$prefix}-{$year}-{$random}";
        } while (static::where('member_id', $memberId)->exists());

        return $memberId;
    }

    /**
     * Get the member's full name
     */
    public function getFullNameAttribute(): string
    {
        $names = [$this->first_name];
        
        if (!empty($this->middle_name)) {
            $names[] = $this->middle_name;
        }
        
        $names[] = $this->last_name;
        
        return implode(' ', $names);
    }

    /**
     * Get the member's display name (first name + last name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the member's formal name (last name, first name)
     */
    public function getFormalNameAttribute(): string
    {
        return $this->last_name . ', ' . $this->first_name;
    }

    /**
     * Check if member email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified === 'yes';
    }

    /**
     * Check if member is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(): bool
    {
        return $this->update(['email_verified' => 'yes']);
    }

    /**
     * Activate member account
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate member account
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Scope active members
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope verified email members
     */
    public function scopeVerified($query)
    {
        return $query->where('email_verified', 'yes');
    }

    /**
     * Scope by membership type
     */
    public function scopeByMembershipType($query, string $type)
    {
        return $query->where('membership_type', $type);
    }

    /**
     * Scope by country
     */
    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Search members by name, email, or member_id
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('member_id', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Relationship with interests
     */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(
            Interest::class,
            'membership_interests',
            'member_id',
            'interest_id'
        )->withTimestamps();
    }

    /**
     * Get member's interest names as array
     */
    public function getInterestNamesAttribute(): array
    {
        return $this->interests->pluck('interest')->toArray();
    }

    /**
     * Check if member has a specific interest
     */
    public function hasInterest($interestId): bool
    {
        if (is_numeric($interestId)) {
            return $this->interests->contains('id', $interestId);
        }

        return $this->interests->contains('interest', $interestId);
    }

    /**
     * Add interests to member
     */
    public function addInterests(array $interestIds): void
    {
        $this->interests()->syncWithoutDetaching($interestIds);
    }

    /**
     * Remove interests from member
     */
    public function removeInterests(array $interestIds): void
    {
        $this->interests()->detach($interestIds);
    }

    /**
     * Sync member interests
     */
    public function syncInterests(array $interestIds): void
    {
        $this->interests()->sync($interestIds);
    }

    /**
     * Get social links as array
     */
    public function getSocialLinksAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

    /**
     * Set social links as JSON
     */
    public function setSocialLinksAttribute($value): void
    {
        $this->attributes['social_links'] = is_array($value) 
            ? json_encode($value) 
            : $value;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if (empty($this->profile_picture)) {
            return null;
        }

        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        return asset('storage/profile_pictures/' . $this->profile_picture);
    }

    /**
     * Get default profile picture URL
     */
    public function getDefaultProfilePictureUrlAttribute(): string
    {
        return asset('img/user-avatar.jpg');
    }

    /**
     * Get display profile picture (falls back to default)
     */
    public function getDisplayProfilePictureAttribute(): string
    {
        return $this->profile_picture_url ?? $this->default_profile_picture_url;
    }

     public function eventCertificates(): HasMany
    {
        return $this->hasMany(EventCertificate::class, 'member_id');
    }

    /**
     * Relationship with certificate downloads
     */
    public function certificateDownloads(): HasMany
    {
        return $this->hasMany(CertificateDownload::class, 'member_id');
    }

    /**
     * Get active event certificates
     */
    public function getActiveEventCertificatesAttribute()
    {
        return $this->eventCertificates()->where('status', 'completed')->get();
    }

    /**
     * Check if member has any event certificates
     */
    public function hasEventCertificates(): bool
    {
        return $this->eventCertificates()->where('status', 'completed')->exists();
    }
}