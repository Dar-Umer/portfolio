<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Summary {{ $project->project_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .summary-box { max-width: 800px; margin: auto; padding: 20px; }
        .header { margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .company-title { font-size: 22px; font-weight: bold; color: #0f172a; }
        .doc-title { font-size: 18px; font-weight: bold; color: #2563eb; text-align: right; }
        .details-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details-table td { vertical-align: top; }
        .section-title { font-size: 13px; font-weight: bold; text-transform: uppercase; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 12px; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th { background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; font-size: 11px; }
        .table-data td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 10px; font-weight: bold; border-radius: 3px; background: #e2e8f0; color: #334155; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-primary { background: #dbeafe; color: #1d4ed8; }
        .footer { font-size: 10px; text-align: center; color: #64748b; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="summary-box">
        @php
            $siteName = \App\Models\Setting::get('site_name', config('app.name', 'The Umer Store'));
            $siteTagline = \App\Models\Setting::get('site_tagline', 'Software Systems & Enterprise Architectures');
            $sellerGstin = \App\Models\Setting::get('seller_gstin', '09ABCDE1234F1Z5');
            $sellerState = \App\Models\Setting::get('seller_state', 'Jammu and Kashmir');
            $contactEmail = \App\Models\Setting::get('contact_email', 'contact@theumer.store');
            $contactPhone = \App\Models\Setting::get('contact_phone', '+91 9622756099');
        @endphp

        <table class="header" width="100%">
            <tr>
                <td>
                    <div class="company-title">{{ $siteName }}</div>
                    <div>{{ $siteTagline }}</div>
                    <div>Email: {{ $contactEmail }} &bull; Tel: {{ $contactPhone }}</div>
                </td>
                <td align="right">
                    <div class="doc-title">PROJECT SPECIFICATION SHEET</div>
                    <div><strong>Project ID:</strong> {{ $project->project_number }}</div>
                    <div><strong>Date Generated:</strong> {{ date('d M Y') }}</div>
                    <div><strong>Current Stage:</strong> {{ $project->status }} ({{ $project->progress_percentage }}%)</div>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="55%">
                    <strong>Client Organization:</strong><br>
                    {{ $project->client->company_name ?? $project->client->user->name }}<br>
                    Attn: {{ $project->client->user->name }}<br>
                    Email: {{ $project->client->user->email }}<br>
                    Client ID: {{ $project->client->client_number }}
                </td>
                <td width="45%">
                    <strong>Project Commercials & Scope:</strong><br>
                    Domain: {{ $project->category }}<br>
                    Agreed Budget: ₹{{ number_format($project->budget, 2) }}<br>
                    Target Delivery: {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'TBD' }}<br>
                    Priority Level: {{ $project->priority }}
                </td>
            </tr>
        </table>

        @if($project->description)
            <div style="margin-bottom: 20px;">
                <div class="section-title">Scope & Executive Summary</div>
                <p style="margin: 0; line-height: 1.6; color: #475569;">{{ $project->description }}</p>
            </div>
        @endif

        <!-- Deliverable Milestones -->
        <div class="section-title">Deliverable Milestones ({{ $project->milestones->count() }})</div>
        <table class="table-data">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Milestone Deliverable</th>
                    <th width="15%">Type</th>
                    <th width="15%">Target Due</th>
                    <th width="20%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->milestones as $index => $m)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $m->title }}</strong>
                            @if($m->description)
                                <div style="color: #64748b; font-size: 10px; margin-top: 2px;">{{ $m->description }}</div>
                            @endif
                        </td>
                        <td>{{ $m->deliverable_type ?? 'Feature' }}</td>
                        <td>{{ $m->due_date ? $m->due_date->format('d M Y') : 'Unscheduled' }}</td>
                        <td>
                            <span class="badge {{ $m->status === 'completed' ? 'badge-success' : 'badge-primary' }}">
                                {{ ucfirst(str_replace('_', ' ', $m->status)) }} ({{ $m->progress }}%)
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b;">No deliverable milestones registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Assigned Engineering Team -->
        @if($project->members->isNotEmpty())
            <div class="section-title">Dedicated Engineering Team</div>
            <table class="table-data">
                <thead>
                    <tr>
                        <th width="40%">Team Member Name</th>
                        <th width="35%">Project Role</th>
                        <th width="25%">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->members as $member)
                        <tr>
                            <td><strong>{{ $member->user->name }}</strong></td>
                            <td>{{ $member->role_in_project }}</td>
                            <td>{{ $member->user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Financial Summary -->
        @if($project->invoices->isNotEmpty())
            <div class="section-title">Commercial & Billing Status</div>
            <table class="table-data">
                <thead>
                    <tr>
                        <th width="30%">Invoice #</th>
                        <th width="25%">Date Issued</th>
                        <th width="25%">Total Amount</th>
                        <th width="20%">Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->invoices as $inv)
                        <tr>
                            <td><strong>{{ $inv->invoice_number }}</strong></td>
                            <td>{{ $inv->issue_date->format('d M Y') }}</td>
                            <td>{{ $inv->currency }} {{ number_format($inv->grand_total, 2) }}</td>
                            <td><span class="badge {{ $inv->status === 'Paid' ? 'badge-success' : 'badge-primary' }}">{{ $inv->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            Official Project Summary Document generated by {{ $siteName }}. Confidential and proprietary to {{ $project->client->company_name ?? $project->client->user->name }}.
        </div>
    </div>
</body>
</html>
