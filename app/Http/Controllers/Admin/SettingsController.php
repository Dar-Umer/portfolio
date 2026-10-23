<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token']);

        foreach ($inputs as $key => $value) {
            $group = match ($key) {
                'ticket_reopen_days' => 'tickets',
                'business_address', 'seller_gstin', 'seller_state', 'default_currency' => 'business',
                'project_categories' => 'projects',
                'recaptcha_site_key', 'recaptcha_secret_key' => 'security',
                default => 'general',
            };
            Setting::set($key, $value, $group);
        }

        AuditService::log('system_settings_updated', null, "Updated system configuration settings");

        return back()->with('success', 'System settings saved successfully.');
    }

    public function auditLogs(Request $request): View
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.settings.audit-logs', compact('logs'));
    }
}
