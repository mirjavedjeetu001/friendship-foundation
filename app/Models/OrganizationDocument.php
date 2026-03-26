<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrganizationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_bn',
        'description',
        'type',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Get file URL
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // Get human-readable file size
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    // Get type label
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deed' => 'Deed',
            'resolution' => 'Resolution',
            'notice' => 'Notice',
            'report' => 'Report',
            default => 'Other',
        };
    }

    // Scope by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
