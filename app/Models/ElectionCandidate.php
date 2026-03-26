<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'position_id',
        'user_id',
        'manifesto',
        'votes_count',
        'is_winner',
    ];

    protected $casts = [
        'is_winner' => 'boolean',
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

    public function votes()
    {
        return $this->hasMany(ElectionVote::class, 'candidate_id');
    }

    // Increment vote count
    public function incrementVote(): void
    {
        $this->increment('votes_count');
    }

    // Get vote percentage
    public function getVotePercentageAttribute(): float
    {
        $totalVotes = $this->position->votes()->count();
        if ($totalVotes === 0) {
            return 0;
        }
        return round(($this->votes_count / $totalVotes) * 100, 1);
    }
}
