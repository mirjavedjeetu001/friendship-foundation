<?php

namespace App\Http\Controllers;

use App\Models\MonthlySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show the settings form
     */
    public function index()
    {
        $settings = MonthlySetting::getSettings();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $settings = MonthlySetting::getSettings();
        
        // Determine which form was submitted and validate accordingly
        if ($request->has('app_name') && !$request->has('bank_name')) {
            // Branding form
            $validated = $request->validate([
                'app_name' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:5120',
            ]);
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($settings->logo) {
                    Storage::disk('public')->delete($settings->logo);
                }
                $validated['logo'] = $request->file('logo')->store('logos', 'public');
            }

            // Handle logo removal
            if ($request->input('remove_logo') == '1' && $settings->logo) {
                Storage::disk('public')->delete($settings->logo);
                $validated['logo'] = null;
            }
            
            $settings->update($validated);
            
        } elseif ($request->has('monthly_contribution_amount')) {
            // General Settings form
            $validated = $request->validate([
                'monthly_contribution_amount' => 'required|numeric|min:1',
                'due_day' => 'required|integer|between:1,28',
                'start_month' => 'nullable|integer|between:1,12',
                'start_year' => 'nullable|integer|between:2020,2050',
            ]);
            
            // Handle checkbox - set to false if not checked
            $validated['force_app_update'] = $request->has('force_app_update');
            
            $settings->update($validated);
            
        } elseif ($request->has('bank_name') || $request->has('account_number') || $request->has('account_holder') || $request->has('routing_number') || $request->has('branch')) {
            // Bank Information form
            $validated = $request->validate([
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'account_holder' => 'nullable|string|max:255',
                'routing_number' => 'nullable|string|max:50',
                'branch' => 'nullable|string|max:255',
            ]);
            
            $settings->update($validated);
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Update bank balance manually (for corrections)
     */
    public function updateBalance(Request $request)
    {
        $validated = $request->validate([
            'bank_balance' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $settings = MonthlySetting::getSettings();
        $settings->update(['bank_balance' => $validated['bank_balance']]);

        // You might want to log this change here

        return back()->with('success', 'Bank balance updated successfully!');
    }
}
