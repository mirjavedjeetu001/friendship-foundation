<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationApprovedMail;
use App\Models\User;
use App\Models\MemberProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MemberController extends Controller
{
    /**
     * Display pending members
     */
    public function pending()
    {
        $members = User::with('memberProfile')
            ->where('status', User::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('members.pending', compact('members'));
    }

    /**
     * Display all members (everyone except super-admin)
     */
    public function index()
    {
        $members = User::with('memberProfile')
            ->where('email', '!=', 'alliedgroup@gmail.com')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('members.index', compact('members'));
    }

    /**
     * Show member profile
     */
    public function show(User $member)
    {
        $member->load('memberProfile');
        return view('members.show', compact('member'));
    }

    /**
     * Approve member
     */
    public function approve(User $member)
    {
        $member->update([
            'status' => User::STATUS_APPROVED,
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'email_verified_at' => now(),
        ]);

        // Send approval email
        $this->sendApprovalEmail($member);

        return back()->with('success', 'Member approved successfully. Email notification sent.');
    }

    /**
     * Reject member
     */
    public function reject(Request $request, User $member)
    {
        $member->update([
            'status' => User::STATUS_REJECTED,
            'is_active' => false,
        ]);

        // Optionally send rejection email with reason
        // $this->sendRejectionEmail($member, $request->reason);

        return back()->with('success', 'Member rejected.');
    }

    /**
     * Update member role
     */
    public function updateRole(Request $request, User $member)
    {
        $request->validate([
            'role' => 'required|in:member,accountant,admin,super-admin'
        ]);

        $member->syncRoles([$request->role]);

        return back()->with('success', 'Member role updated successfully.');
    }

    /**
     * Download member documents as ZIP
     */
    public function downloadDocuments(User $member)
    {
        $member->load('memberProfile');
        $profile = $member->memberProfile;

        if (!$profile) {
            return back()->with('error', 'No profile found for this member.');
        }

        $zipFileName = 'member_' . $member->id . '_' . str_replace(' ', '_', $member->name) . '_documents.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Create temp directory if not exists
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        // Add member info text file
        $info = "Member Information\n";
        $info .= "==================\n\n";
        $info .= "Name: {$member->name}\n";
        $info .= "Name (Bangla): {$profile->full_name_bangla}\n";
        $info .= "Email: {$member->email}\n";
        $info .= "Phone: {$member->phone}\n";
        $info .= "Secondary Phone: {$profile->phone_secondary}\n";
        $info .= "Father's Name: {$profile->father_name}\n";
        $info .= "Mother's Name: {$profile->mother_name}\n";
        $info .= "Date of Birth: {$profile->date_of_birth?->format('d/m/Y')}\n";
        $info .= "Gender: {$profile->gender}\n";
        $info .= "Blood Group: {$profile->blood_group}\n";
        $info .= "NID Number: {$profile->nid_number}\n\n";
        $info .= "Present Address: {$profile->present_address}\n";
        $info .= "Permanent Address: {$profile->permanent_address}\n\n";
        $info .= "Occupation: {$profile->occupation}\n";
        $info .= "Designation: {$profile->designation}\n";
        $info .= "Organization: {$profile->organization}\n\n";
        $info .= "Emergency Contact: {$profile->emergency_contact_name} ({$profile->emergency_contact_relation})\n";
        $info .= "Emergency Phone: {$profile->emergency_contact_phone}\n\n";
        $info .= "NOMINEE INFORMATION\n";
        $info .= "-------------------\n";
        $info .= "Nominee Name: {$profile->nominee_name}\n";
        $info .= "Relation: {$profile->nominee_relation}\n";
        $info .= "Phone: {$profile->nominee_phone}\n";
        $info .= "NID: {$profile->nominee_nid_number}\n";
        $info .= "Address: {$profile->nominee_address}\n\n";
        $info .= "BANKING INFORMATION\n";
        $info .= "-------------------\n";
        $info .= "Bank: {$profile->bank_name}\n";
        $info .= "Branch: {$profile->bank_branch}\n";
        $info .= "Account Name: {$profile->bank_account_name}\n";
        $info .= "Account Number: {$profile->bank_account_number}\n";
        $info .= "Routing Number: {$profile->bank_routing_number}\n";
        $info .= "Account Type: {$profile->account_type}\n";
        $info .= "Mobile Banking: {$profile->mobile_banking_provider} - {$profile->mobile_banking_number}\n";

        $zip->addFromString('member_info.txt', $info);

        // Add photos
        $photos = [
            'passport_photo' => 'passport_photo',
            'signature_photo' => 'signature',
            'nid_front_photo' => 'nid_front',
            'nid_back_photo' => 'nid_back',
            'nominee_photo' => 'nominee_photo',
            'nominee_nid_front_photo' => 'nominee_nid_front',
            'nominee_nid_back_photo' => 'nominee_nid_back',
        ];

        foreach ($photos as $field => $filename) {
            if ($profile->$field && Storage::disk('public')->exists($profile->$field)) {
                $ext = pathinfo($profile->$field, PATHINFO_EXTENSION);
                $zip->addFile(Storage::disk('public')->path($profile->$field), $filename . '.' . $ext);
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Download all members data
     */
    public function downloadAll()
    {
        $members = User::with('memberProfile')
            ->where('status', User::STATUS_APPROVED)
            ->where('email', '!=', 'alliedgroup@gmail.com')
            ->get();

        $zipFileName = 'all_members_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        // Create CSV with all member data
        $csv = "ID,Name,Name Bangla,Email,Phone,Father,Mother,DOB,Gender,Blood Group,NID,Present Address,Permanent Address,Occupation,Organization,Emergency Contact,Emergency Phone,Nominee Name,Nominee Relation,Nominee Phone,Nominee NID,Bank,Branch,Account Name,Account Number,Routing,Account Type,Mobile Banking\n";

        foreach ($members as $member) {
            $p = $member->memberProfile;
            if (!$p) continue;
            
            $csv .= "\"{$member->id}\",";
            $csv .= "\"{$member->name}\",";
            $csv .= "\"{$p->full_name_bangla}\",";
            $csv .= "\"{$member->email}\",";
            $csv .= "\"{$member->phone}\",";
            $csv .= "\"{$p->father_name}\",";
            $csv .= "\"{$p->mother_name}\",";
            $csv .= "\"{$p->date_of_birth?->format('Y-m-d')}\",";
            $csv .= "\"{$p->gender}\",";
            $csv .= "\"{$p->blood_group}\",";
            $csv .= "\"{$p->nid_number}\",";
            $csv .= "\"" . str_replace('"', '""', $p->present_address ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $p->permanent_address ?? '') . "\",";
            $csv .= "\"{$p->occupation}\",";
            $csv .= "\"{$p->organization}\",";
            $csv .= "\"{$p->emergency_contact_name}\",";
            $csv .= "\"{$p->emergency_contact_phone}\",";
            $csv .= "\"{$p->nominee_name}\",";
            $csv .= "\"{$p->nominee_relation}\",";
            $csv .= "\"{$p->nominee_phone}\",";
            $csv .= "\"{$p->nominee_nid_number}\",";
            $csv .= "\"{$p->bank_name}\",";
            $csv .= "\"{$p->bank_branch}\",";
            $csv .= "\"{$p->bank_account_name}\",";
            $csv .= "\"{$p->bank_account_number}\",";
            $csv .= "\"{$p->bank_routing_number}\",";
            $csv .= "\"{$p->account_type}\",";
            $csv .= "\"{$p->mobile_banking_provider} {$p->mobile_banking_number}\"\n";
        }

        $zip->addFromString('all_members.csv', "\xEF\xBB\xBF" . $csv); // BOM for Excel UTF-8

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Send approval email
     */
    private function sendApprovalEmail(User $member)
    {
        try {
            Mail::to($member->email)->send(new RegistrationApprovedMail($member));
        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
    }
}
