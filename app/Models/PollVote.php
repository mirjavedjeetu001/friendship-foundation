<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'option_id',
        'voter_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function option()
    {
        return $this->belongsTo(PollOption::class, 'option_id');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}
