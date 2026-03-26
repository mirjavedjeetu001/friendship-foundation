<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'option_text',
        'option_text_bn',
        'votes_count',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class, 'option_id');
    }

    public function incrementVote(): void
    {
        $this->increment('votes_count');
    }

    public function getVotePercentageAttribute(): float
    {
        $totalVotes = $this->election->pollVotes()->count();
        if ($totalVotes === 0) {
            return 0;
        }
        return round(($this->votes_count / $totalVotes) * 100, 1);
    }
}
