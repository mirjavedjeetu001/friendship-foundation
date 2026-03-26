<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_bn',
        'description',
        'description_bn',
        'type',
        'start_time',
        'end_time',
        'status',
        'term_years',
        'created_by',
        'results_published',
        'results_published_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'results_published' => 'boolean',
        'results_published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions()
    {
        return $this->hasMany(ElectionPosition::class)->orderBy('order');
    }

    public function candidates()
    {
        return $this->hasMany(ElectionCandidate::class);
    }

    public function votes()
    {
        return $this->hasMany(ElectionVote::class);
    }

    public function pollOptions()
    {
        return $this->hasMany(PollOption::class);
    }

    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function committeeMembers()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    // Check if election is currently active
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' && 
               $now->gte($this->start_time) && 
               $now->lte($this->end_time);
    }

    // Check if election has ended
    public function hasEnded(): bool
    {
        return Carbon::now()->gt($this->end_time) || $this->status === 'completed';
    }

    // Check if election is upcoming
    public function isUpcoming(): bool
    {
        return Carbon::now()->lt($this->start_time) && in_array($this->status, ['upcoming', 'active']);
    }

    // Get total votes count
    public function getTotalVotesAttribute(): int
    {
        if ($this->type === 'election') {
            return $this->votes()->distinct('voter_id')->count('voter_id');
        }
        return $this->pollVotes()->count();
    }

    // Check if user has voted
    public function hasUserVoted($userId): bool
    {
        if ($this->type === 'election') {
            return $this->votes()->where('voter_id', $userId)->exists();
        }
        return $this->pollVotes()->where('voter_id', $userId)->exists();
    }

    // Auto-update status based on time
    public function updateStatus(): void
    {
        $now = Carbon::now();
        
        // Don't auto-update cancelled or paused elections
        if (in_array($this->status, ['cancelled', 'paused'])) {
            return;
        }

        if ($now->lt($this->start_time)) {
            $this->status = 'upcoming';
        } elseif ($now->gte($this->start_time) && $now->lte($this->end_time)) {
            $this->status = 'active';
        } elseif ($now->gt($this->end_time)) {
            if ($this->status !== 'completed') {
                $this->status = 'completed';
                $this->determineWinners();
            }
        }
        
        $this->save();
    }

    // Determine winners after election ends
    public function determineWinners(): void
    {
        if ($this->type !== 'election') {
            return;
        }

        foreach ($this->positions as $position) {
            $topCandidate = $position->candidates()
                ->where('votes_count', '>', 0) // Only consider candidates with at least 1 vote
                ->orderByDesc('votes_count')
                ->first();

            if ($topCandidate) {
                // Mark as winner
                $topCandidate->is_winner = true;
                $topCandidate->save();

                // Add to committee members
                CommitteeMember::updateOrCreate(
                    [
                        'election_id' => $this->id,
                        'position_id' => $position->id,
                    ],
                    [
                        'user_id' => $topCandidate->user_id,
                        'term_start' => $this->end_time,
                        'term_end' => Carbon::parse($this->end_time)->addYears($this->term_years),
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Check if election has any votes
     */
    public function hasVotes(): bool
    {
        if ($this->type === 'election') {
            return $this->votes()->exists();
        }
        return $this->pollVotes()->exists();
    }

    // Scope for active elections
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now);
    }

    // Scope for completed elections
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
