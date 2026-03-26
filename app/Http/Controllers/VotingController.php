<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\ElectionCandidate;
use App\Models\ElectionVote;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\CommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VotingController extends Controller
{
    /**
     * Display all elections/polls for members
     */
    public function index()
    {
        // Update all election statuses
        Election::all()->each(fn($e) => $e->updateStatus());

        $activeElections = Election::with(['positions.candidates.user', 'pollOptions'])
            ->active()
            ->orderBy('end_time')
            ->get();

        $upcomingElections = Election::with(['positions.candidates.user.profile', 'pollOptions'])
            ->where('status', 'upcoming')
            ->orderBy('start_time')
            ->get();

        $completedElections = Election::with(['positions.winner.user', 'pollOptions'])
            ->completed()
            ->orderByDesc('end_time')
            ->take(5)
            ->get();

        return view('elections.index', compact('activeElections', 'upcomingElections', 'completedElections'));
    }

    /**
     * Show voting page for an election
     */
    public function show(Election $election)
    {
        $election->updateStatus();

        // Load relationships
        $election->load(['positions.candidates.user.profile', 'pollOptions']);

        // If election hasn't started yet, show preview page
        if ($election->isUpcoming()) {
            return view('elections.preview', compact('election'));
        }

        if (!$election->isActive()) {
            if ($election->hasEnded()) {
                return redirect()->route('elections.results', $election)
                    ->with('info', 'This election has ended. View the results.');
            }
            return redirect()->route('elections.index')
                ->with('info', 'This election is not active.');
        }

        $hasVoted = $election->hasUserVoted(auth()->id());

        // Get user's existing votes
        $userVotes = [];
        if ($election->type === 'election') {
            $userVotes = ElectionVote::where('election_id', $election->id)
                ->where('voter_id', auth()->id())
                ->pluck('candidate_id', 'position_id')
                ->toArray();
        }

        return view('elections.vote', compact('election', 'hasVoted', 'userVotes'));
    }

    /**
     * Cast vote for election
     */
    public function vote(Request $request, Election $election)
    {
        $election->updateStatus();

        if (!$election->isActive()) {
            return back()->with('error', 'ভোট দেওয়ার সময় শেষ হয়ে গেছে');
        }

        if ($election->type === 'election') {
            return $this->voteElection($request, $election);
        }

        return $this->votePoll($request, $election);
    }

    /**
     * Cast vote in committee election
     */
    private function voteElection(Request $request, Election $election)
    {
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'required|exists:election_candidates,id',
        ], [
            'votes.required' => 'অন্তত একটি পদে ভোট দিন',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->votes as $positionId => $candidateId) {
                // Check if already voted for this position
                $existingVote = ElectionVote::where([
                    'election_id' => $election->id,
                    'position_id' => $positionId,
                    'voter_id' => auth()->id(),
                ])->first();

                if ($existingVote) {
                    continue; // Skip if already voted
                }

                // Verify candidate belongs to this position
                $candidate = ElectionCandidate::where([
                    'id' => $candidateId,
                    'position_id' => $positionId,
                    'election_id' => $election->id,
                ])->first();

                if (!$candidate) {
                    continue;
                }

                // Create vote
                ElectionVote::create([
                    'election_id' => $election->id,
                    'position_id' => $positionId,
                    'candidate_id' => $candidateId,
                    'voter_id' => auth()->id(),
                ]);

                // Increment vote count
                $candidate->incrementVote();
            }

            DB::commit();

            return redirect()->route('elections.results', $election)
                ->with('success', 'আপনার ভোট সফলভাবে জমা হয়েছে!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'ভোট দিতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Cast vote in poll
     */
    private function votePoll(Request $request, Election $election)
    {
        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ], [
            'option_id.required' => 'একটি অপশন নির্বাচন করুন',
        ]);

        // Check if already voted
        if ($election->hasUserVoted(auth()->id())) {
            return back()->with('error', 'আপনি ইতিমধ্যে ভোট দিয়েছেন');
        }

        $option = PollOption::where([
            'id' => $request->option_id,
            'election_id' => $election->id,
        ])->first();

        if (!$option) {
            return back()->with('error', 'অবৈধ অপশন');
        }

        DB::beginTransaction();

        try {
            PollVote::create([
                'election_id' => $election->id,
                'option_id' => $request->option_id,
                'voter_id' => auth()->id(),
            ]);

            $option->incrementVote();

            DB::commit();

            return redirect()->route('elections.results', $election)
                ->with('success', 'আপনার ভোট সফলভাবে জমা হয়েছে!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'ভোট দিতে সমস্যা হয়েছে');
        }
    }

    /**
     * View live results
     */
    public function results(Election $election)
    {
        $election->updateStatus();
        $election->load([
            'positions.candidates.user.profile',
            'positions.winner.user',
            'pollOptions',
            'committeeMembers.user.profile'
        ]);

        $hasVoted = $election->hasUserVoted(auth()->id());
        $totalVoters = $election->total_votes;

        return view('elections.results', compact('election', 'hasVoted', 'totalVoters'));
    }

    /**
     * Get live results via AJAX
     */
    public function liveResults(Election $election)
    {
        $election->updateStatus();

        if ($election->type === 'election') {
            $data = $election->positions->map(function ($position) {
                return [
                    'position_id' => $position->id,
                    'position_name' => $position->name_bn ?? $position->name,
                    'candidates' => $position->candidates->map(function ($candidate) {
                        return [
                            'id' => $candidate->id,
                            'name' => $candidate->user->name,
                            'votes' => $candidate->votes_count,
                            'percentage' => $candidate->vote_percentage,
                            'is_winner' => $candidate->is_winner,
                        ];
                    }),
                ];
            });
        } else {
            $data = [
                'options' => $election->pollOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text_bn ?? $option->option_text,
                        'votes' => $option->votes_count,
                        'percentage' => $option->vote_percentage,
                    ];
                }),
            ];
        }

        return response()->json([
            'status' => $election->status,
            'total_votes' => $election->total_votes,
            'data' => $data,
            'has_ended' => $election->hasEnded(),
            'remaining_time' => $election->isActive() 
                ? Carbon::now()->diffInSeconds($election->end_time) 
                : 0,
        ]);
    }

    /**
     * View election history
     */
    public function history()
    {
        $elections = Election::with(['positions.winner.user', 'pollOptions'])
            ->completed()
            ->orderByDesc('end_time')
            ->paginate(10);

        return view('elections.history', compact('elections'));
    }

    /**
     * View current committee (About Allied Group)
     */
    public function committee()
    {
        // Get active committee members
        $committeeMembers = CommitteeMember::with(['user.profile', 'position', 'election'])
            ->active()
            ->orderBy('position_id')
            ->get()
            ->groupBy('election_id');

        // Get latest completed election
        $latestElection = Election::with(['positions.winner.user.profile'])
            ->where('type', 'election')
            ->where('status', 'completed')
            ->orderByDesc('end_time')
            ->first();

        // Get all past elections for history
        $pastElections = Election::with(['positions.winner.user', 'committeeMembers'])
            ->where('type', 'election')
            ->where('status', 'completed')
            ->orderByDesc('end_time')
            ->get();

        // Get committee member user IDs from latest election
        $committeeUserIds = [];
        if ($latestElection) {
            foreach ($latestElection->positions as $position) {
                if ($position->winner) {
                    $committeeUserIds[] = $position->winner->user_id;
                }
            }
        }

        // Get all approved members except committee members and super-admin
        $otherMembers = \App\Models\User::with('memberProfile')
            ->whereHas('memberProfile')
            ->where('status', 'approved')
            ->whereNotIn('id', $committeeUserIds)
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'super-admin');
            })
            ->orderBy('name')
            ->get();

        return view('elections.committee', compact('committeeMembers', 'latestElection', 'pastElections', 'otherMembers'));
    }
}
