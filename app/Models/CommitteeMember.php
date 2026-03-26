<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'position_id',
        'user_id',
        'term_start',
        'term_end',
        'is_active',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end' => 'date',
        'is_active' => 'boolean',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function position()
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if term is still active
    public function isTermActive(): bool
    {
        return $this->is_active && Carbon::now()->lte($this->term_end);
    }

    // Scope for active members
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('term_end', '>=', Carbon::now());
    }

    // Scope for expired terms
    public function scopeExpired($query)
    {
        return $query->where('term_end', '<', Carbon::now());
    }
}
