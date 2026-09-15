<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchSetting;
use App\Models\DropdownOption;
use App\Services\SMSOnlineGhService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $dropdownCount = DropdownOption::count();
        $churchSetting = ChurchSetting::current();

        return view('admin.settings.index', compact('dropdownCount', 'churchSetting'));
    }

    public function updateChurchInfo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'pastor_signature' => 'nullable|image|max:2048',
            'finance_signature' => 'nullable|image|max:2048',
        ]);

        $churchSetting = ChurchSetting::current();

        if ($request->hasFile('logo')) {
            if ($churchSetting->logo_path && Storage::disk('public')->exists($churchSetting->logo_path)) {
                Storage::disk('public')->delete($churchSetting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('church', 'public');
        }

        if ($request->hasFile('pastor_signature')) {
            if ($churchSetting->pastor_signature_path && Storage::disk('public')->exists($churchSetting->pastor_signature_path)) {
                Storage::disk('public')->delete($churchSetting->pastor_signature_path);
            }
            $validated['pastor_signature_path'] = $request->file('pastor_signature')->store('church', 'public');
        }

        if ($request->hasFile('finance_signature')) {
            if ($churchSetting->finance_signature_path && Storage::disk('public')->exists($churchSetting->finance_signature_path)) {
                Storage::disk('public')->delete($churchSetting->finance_signature_path);
            }
            $validated['finance_signature_path'] = $request->file('finance_signature')->store('church', 'public');
        }

        unset($validated['logo'], $validated['pastor_signature'], $validated['finance_signature']);

        $churchSetting->fill($validated);
        $churchSetting->save();

        return back()->with('success', 'Church information updated.');
    }

    public function checkSMSBalance()
    {
        $balance = SMSOnlineGhService::checkSMSBalance(); // 1250; // Fetch from SMS provider API

        return response()->json([
            'success' => true,
            'balance' => $balance,
        ]);
    }
}
