<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\ElectionCandidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectionController extends Controller
{
    /**
     * Display all elections/polls (admin view)
     */
    public function index()
    {
        $elections = Election::with(['creator', 'positions'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Update status for all elections
        foreach ($elections as $election) {
            $election->updateStatus();
        }

        return view('admin.elections.index', compact('elections'));
    }

    /**
     * Show form to create new election/poll
     */
    public function create()
    {
        $members = User::whereHas('memberProfile')
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.elections.create', compact('members'));
    }

    /**
     * Store new election/poll
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'type' => 'required|in:election,poll',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'term_years' => 'required_if:type,election|integer|min:1|max:5',
            
            // For elections
            'positions' => 'required_if:type,election|array',
            'positions.*.name' => 'required_if:type,election|string|max:255',
            'positions.*.name_bn' => 'nullable|string|max:255',
            'positions.*.candidates' => 'required_if:type,election|array|min:1',
            
            // For polls
            'options' => 'required_if:type,poll|array|min:2',
            'options.*' => 'required_if:type,poll|string|max:255',
        ], [
            'title.required' => 'Election/Poll title is required',
            'start_time.required' => 'Start time is required',
            'end_time.required' => 'End time is required',
            'end_time.after' => 'End time must be after start time',
            'positions.required_if' => 'Add at least one position for election',
            'positions.*.candidates.required_if' => 'Select at least one candidate for each position',
            'options.required_if' => 'Add at least two options for poll',
        ]);

        DB::beginTransaction();

        try {
            $election = Election::create([
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'description' => $request->description,
                'description_bn' => $request->description_bn,
                'type' => $request->type,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'upcoming',
                'term_years' => $request->type === 'election' ? $request->term_years : 0,
                'created_by' => auth()->id(),
            ]);

            if ($request->type === 'election') {
                // Create positions and candidates
                $order = 0;
                foreach ($request->positions as $positionData) {
                    $position = ElectionPosition::create([
                        'election_id' => $election->id,
                        'name' => $positionData['name'],
                        'name_bn' => $positionData['name_bn'] ?? null,
                        'description' => $positionData['description'] ?? null,
                        'order' => $order++,
                    ]);

                    // Add candidates
                    foreach ($positionData['candidates'] as $userId) {
                        ElectionCandidate::create([
                            'election_id' => $election->id,
                            'position_id' => $position->id,
                            'user_id' => $userId,
                        ]);
                    }
                }
            } else {
                // Create poll options
                foreach ($request->options as $optionText) {
                    $election->pollOptions()->create([
                        'option_text' => $optionText,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.elections.show', $election)
                ->with('success', 'Election/Poll created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Show election details
     */
    public function show(Election $election)
    {
        $election->updateStatus();
        $election->load(['positions.candidates.user', 'pollOptions', 'creator']);
        
        return view('admin.elections.show', compact('election'));
    }

    /**
     * Edit election (only if not started)
     */
    public function edit(Election $election)
    {
        if (!in_array($election->status, ['upcoming', 'draft', 'paused'])) {
            return back()->with('error', 'Cannot edit an active or completed election. Pause it first.');
        }

        $election->load(['positions.candidates', 'pollOptions']);
        $members = User::whereHas('memberProfile')
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.elections.edit', compact('election', 'members'));
    }

    /**
     * Update election
     */
    public function update(Request $request, Election $election)
    {
        if (!in_array($election->status, ['upcoming', 'draft', 'paused'])) {
            return back()->with('error', 'Cannot edit an active or completed election. Pause it first.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Determine new status based on times
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $now = Carbon::now();
        
        $newStatus = $election->status;
        if ($election->status === 'paused' || $election->status === 'draft') {
            if ($startTime->isFuture()) {
                $newStatus = 'upcoming';
            } elseif ($now->between($startTime, $endTime)) {
                $newStatus = 'active';
            }
        }

        $election->update([
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'term_years' => $request->term_years ?? $election->term_years,
            'status' => $newStatus,
        ]);

        return redirect()->route('admin.elections.show', $election)
            ->with('success', 'Election updated successfully! Status: ' . ucfirst($newStatus));
    }

    /**
     * Cancel election
     */
    public function cancel(Election $election)
    {
        if ($election->status === 'completed') {
            return back()->with('error', 'Completed elections cannot be cancelled');
        }

        $election->update(['status' => 'cancelled']);

        return back()->with('success', 'Election has been cancelled');
    }

    /**
     * Start election manually
     */
    public function start(Election $election)
    {
        if ($election->status !== 'upcoming') {
            return back()->with('error', 'This election cannot be started');
        }

        $election->update([
            'status' => 'active',
            'start_time' => Carbon::now(),
        ]);

        return back()->with('success', 'Election has started!');
    }

    /**
     * End election manually
     */
    public function end(Election $election)
    {
        if ($election->status !== 'active') {
            return back()->with('error', 'This election cannot be ended');
        }

        $election->update([
            'status' => 'completed',
            'end_time' => Carbon::now(),
        ]);

        $election->determineWinners();

        return back()->with('success', 'Election has ended and results are published!');
    }

    /**
     * Pause/Stop an active election for editing
     */
    public function stop(Election $election)
    {
        if ($election->status !== 'active') {
            return back()->with('error', 'Only active elections can be paused');
        }

        $election->update([
            'status' => 'paused',
        ]);

        return back()->with('success', 'Election has been paused. You can now edit it.');
    }

    /**
     * Resume a paused election
     */
    public function resume(Election $election)
    {
        if ($election->status !== 'paused') {
            return back()->with('error', 'Only paused elections can be resumed');
        }

        // Determine correct status based on current time
        $now = Carbon::now();
        if ($election->start_time->isFuture()) {
            $newStatus = 'upcoming';
        } elseif ($now->between($election->start_time, $election->end_time)) {
            $newStatus = 'active';
        } else {
            $newStatus = 'completed';
        }

        $election->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', 'Election resumed! Status: ' . ucfirst($newStatus));
    }

    /**
     * View results
     */
    public function results(Election $election)
    {
        $election->updateStatus();
        $election->load(['positions.candidates.user', 'pollOptions', 'committeeMembers.user']);

        return view('admin.elections.results', compact('election'));
    }

    /**
     * Publish election results
     */
    public function publish(Election $election)
    {
        if ($election->status !== 'completed') {
            return back()->with('error', 'Only completed elections can be published');
        }

        $election->update([
            'results_published' => true,
            'results_published_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Results published! Committee page will now show these winners.');
    }

    /**
     * Unpublish election results
     */
    public function unpublish(Election $election)
    {
        $election->update([
            'results_published' => false,
            'results_published_at' => null,
        ]);

        return back()->with('success', 'Results unpublished. Committee page will not show these winners.');
    }

    /**
     * Toggle winner status for a candidate
     */
    public function toggleWinner(Election $election, ElectionCandidate $candidate)
    {
        // If already winner, remove winner status
        if ($candidate->is_winner) {
            $candidate->update(['is_winner' => false]);
            return back()->with('success', 'Winner status removed from ' . $candidate->user->name);
        }

        // Remove current winner for this position
        ElectionCandidate::where('position_id', $candidate->position_id)
            ->where('is_winner', true)
            ->update(['is_winner' => false]);

        // Set new winner
        $candidate->update(['is_winner' => true]);

        return back()->with('success', $candidate->user->name . ' set as winner!');
    }

    /**
     * View all election history
     */
    public function history()
    {
        $elections = Election::with(['positions.winner.user', 'creator'])
            ->where('status', 'completed')
            ->orderByDesc('end_time')
            ->paginate(10);

        return view('admin.elections.history', compact('elections'));
    }

    /**
     * Add candidate to position
     */
    public function addCandidate(Request $request, Election $election)
    {
        if ($election->status !== 'upcoming' && $election->status !== 'draft') {
            return back()->with('error', 'Cannot add candidates to an election that has already started');
        }

        $request->validate([
            'position_id' => 'required|exists:election_positions,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if candidate already exists
        $exists = ElectionCandidate::where([
            'election_id' => $election->id,
            'position_id' => $request->position_id,
            'user_id' => $request->user_id,
        ])->exists();

        if ($exists) {
            return back()->with('error', 'This member is already a candidate for this position');
        }

        ElectionCandidate::create([
            'election_id' => $election->id,
            'position_id' => $request->position_id,
            'user_id' => $request->user_id,
        ]);

        return back()->with('success', 'Candidate added successfully');
    }

    /**
     * Remove candidate
     */
    public function removeCandidate(Election $election, ElectionCandidate $candidate)
    {
        if ($election->status !== 'upcoming' && $election->status !== 'draft') {
            return back()->with('error', 'Cannot remove candidates from an election that has already started');
        }

        $candidate->delete();

        return back()->with('success', 'Candidate removed successfully');
    }

    /**
     * Delete election (only draft)
     */
    public function destroy(Election $election)
    {
        // Cannot delete active elections - must pause or end first
        if ($election->status === 'active') {
            return back()->with('error', 'Cannot delete an active election. Please pause or end it first.');
        }

        $electionTitle = $election->title;
        $election->delete();

        return redirect()->route('admin.elections.index')
            ->with('success', "Election '{$electionTitle}' and all related data (positions, candidates, votes, committee) deleted permanently!");
    }
}
