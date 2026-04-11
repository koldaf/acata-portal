<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CertificateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_type',
        'signatory_name',
        'signatory_title',
        'signature_path',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'updated_by');
    }

    public function getSignatureDataUriAttribute(): ?string
    {
        if (!$this->signature_path || !Storage::disk('public')->exists($this->signature_path)) {
            return null;
        }

        $filePath = Storage::disk('public')->path($this->signature_path);
        $mimeType = File::mimeType($filePath) ?: 'image/png';
        $contents = Storage::disk('public')->get($this->signature_path);

        return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
    }
}
