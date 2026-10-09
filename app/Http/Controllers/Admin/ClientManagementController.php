<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Common\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClientManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClientProfile::with(['user', 'projects', 'tickets', 'invoices']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_number', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->status);
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'client_category' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Customer');

        $clientNumber = DocumentNumberService::generate('CLT', 'client_profiles', 'client_number');

        $client = ClientProfile::create([
            'user_id' => $user->id,
            'client_number' => $clientNumber,
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'billing_address' => $validated['billing_address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
            'tax_number' => $validated['tax_number'],
            'client_category' => $validated['client_category'],
            'account_status' => 'active',
            'notes' => $validated['notes'],
        ]);

        AuditService::log('client_created', $client, "Created client {$client->client_number} ({$user->name})");

        return redirect()->route('admin.clients.show', $client)->with('success', "Client {$client->client_number} created successfully.");
    }

    public function show(ClientProfile $client): View
    {
        $client->load([
            'user',
            'projects.milestones',
            'tickets.status',
            'quotations',
            'invoices.allocations',
            'payments',
        ]);

        $totalInvoiced = $client->invoices->sum('grand_total');
        $totalPaid = $client->payments->where('status', 'completed')->sum('amount');
        $outstanding = $client->invoices->sum('balance_due');

        return view('admin.clients.show', compact('client', 'totalInvoiced', 'totalPaid', 'outstanding'));
    }

    public function edit(ClientProfile $client): View
    {
        $client->load('user');
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, ClientProfile $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'client_category' => ['required', 'string', 'max:50'],
            'account_status' => ['required', 'in:active,suspended,archived'],
            'notes' => ['nullable', 'string'],
        ]);

        $client->user->update(['name' => $validated['name']]);

        $client->update([
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'billing_address' => $validated['billing_address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
            'tax_number' => $validated['tax_number'],
            'client_category' => $validated['client_category'],
            'account_status' => $validated['account_status'],
            'notes' => $validated['notes'],
        ]);

        AuditService::log('client_updated', $client, "Updated client profile {$client->client_number}");

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully.');
    }
}
