<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'timezone' => 'required|string|max:100',
        ]);

        $settings = [
            'system_name' => $request->system_name,
            'company_name' => $request->company_name,
            'timezone' => $request->timezone,
            'risk_notification' => $request->has('risk_notification') ? '1' : '0',
            'weather_notification' => $request->has('weather_notification') ? '1' : '0',
            'currency_notification' => $request->has('currency_notification') ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
