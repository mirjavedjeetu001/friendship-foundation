<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Personal Information
        'full_name_bangla',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'occupation',
        'designation',
        'organization',
        // Contact
        'phone_secondary',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        // Address
        'present_address',
        'permanent_address',
        // NID
        'nid_number',
        'nid_front_photo',
        'nid_back_photo',
        // Passport Photo
        'passport_photo',
        // Nominee
        'nominee_name',
        'nominee_relation',
        'nominee_phone',
        'nominee_nid_number',
        'nominee_photo',
        'nominee_nid_front_photo',
        'nominee_nid_back_photo',
        'nominee_address',
        // Banking
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'bank_routing_number',
        'account_type',
        'mobile_banking_provider',
        'mobile_banking_number',
        // Signature
        'signature_photo',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get photo URL for a field
     */
    public function getPhotoUrl(string $field): ?string
    {
        if (!$this->$field) {
            return null;
        }
        return asset('storage/' . $this->$field);
    }

    public function getPassportPhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('passport_photo');
    }

    public function getNidFrontPhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('nid_front_photo');
    }

    public function getNidBackPhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('nid_back_photo');
    }

    public function getNomineePhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('nominee_photo');
    }

    public function getNomineeNidFrontPhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('nominee_nid_front_photo');
    }

    public function getNomineeNidBackPhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('nominee_nid_back_photo');
    }

    public function getSignaturePhotoUrlAttribute(): ?string
    {
        return $this->getPhotoUrl('signature_photo');
    }
}
