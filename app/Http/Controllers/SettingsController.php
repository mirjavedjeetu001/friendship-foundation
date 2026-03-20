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
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'monthly_contribution_amount' => 'required|numeric|min:1',
            'due_day' => 'required|integer|between:1,28',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_holder' => 'nullable|string|max:255',
        ]);

        $settings = MonthlySetting::getSettings();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
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
