<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'name',
        'name_bn',
        'description',
        'order',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidates()
    {
        return $this->hasMany(ElectionCandidate::class, 'position_id');
    }

    public function votes()
    {
        return $this->hasMany(ElectionVote::class, 'position_id');
    }

    public function winner()
    {
        return $this->hasOne(ElectionCandidate::class, 'position_id')->where('is_winner', true);
    }

    public function committeeMember()
    {
        return $this->hasOne(CommitteeMember::class, 'position_id')->where('is_active', true);
    }
}
