<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .quote-box { max-width: 800px; margin: auto; padding: 20px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .company-title { font-size: 24px; font-weight: bold; color: #0f172a; }
        .doc-title { font-size: 20px; font-weight: bold; color: #2563eb; text-align: right; }
        .details-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .details-table td { vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background: #f8fafc; border: 1px solid #cbd5e1; padding: 8px; text-align: left; font-size: 12px; }
        .items-table td { border: 1px solid #cbd5e1; padding: 8px; font-size: 12px; }
        .totals-table { width: 40%; margin-left: auto; border-collapse: collapse; margin-bottom: 30px; }
        .totals-table td { padding: 6px; }
        .totals-table .grand-total { font-size: 15px; font-weight: bold; border-top: 2px solid #2563eb; color: #0f172a; }
        .footer { font-size: 11px; text-align: center; color: #64748b; margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="quote-box">
        <table class="header" width="100%">
            <tr>
                <td>
                    <div class="company-title">The Umer Store</div>
                    <div>Software Systems & Enterprise Architectures</div>
                    <div>GSTIN: 09ABCDE1234F1Z5 &bull; State: Uttar Pradesh</div>
                </td>
                <td align="right">
                    <div class="doc-title">PROPOSAL & QUOTATION</div>
                    <div><strong>Quotation #:</strong> {{ $quotation->quotation_number }}</div>
                    <div><strong>Issue Date:</strong> {{ $quotation->issue_date->format('d M Y') }}</div>
                    <div><strong>Valid Until:</strong> {{ $quotation->expiry_date->format('d M Y') }}</div>
                    <div><strong>Status:</strong> {{ $quotation->status }}</div>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="55%">
                    <strong>Prepared For:</strong><br>
                    {{ $quotation->client->company_name ?? $quotation->client->user->name }}<br>
                    Attn: {{ $quotation->client->user->name }}<br>
                    {{ $quotation->client->billing_address }}<br>
                    {{ $quotation->client->city }}, {{ $quotation->client->state }} - {{ $quotation->client->postal_code }}<br>
                    GSTIN / Tax ID: {{ $quotation->client->tax_number ?? 'N/A' }}
                </td>
                <td width="45%">
                    <strong>Project / Scope:</strong><br>
                    {{ $quotation->project->name ?? 'Custom Software Development Scope' }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Deliverable / Item</th>
                    <th width="12%">Qty</th>
                    <th width="18%">Unit Price</th>
                    <th width="20%" align="right">Total ({{ $quotation->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td align="right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td align="right">{{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            @if($quotation->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td align="right">-{{ number_format($quotation->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Tax Total:</td>
                <td align="right">{{ number_format($quotation->tax_total, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Estimated Total:</td>
                <td align="right">{{ $quotation->currency }} {{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
        </table>

        @if($quotation->terms_and_conditions)
            <div style="font-size: 11px; margin-top: 20px;">
                <strong>Terms & Conditions:</strong><br>
                {{ $quotation->terms_and_conditions }}
            </div>
        @endif

        <div class="footer">
            Confidential proposal prepared by The Umer Store. Valid until {{ $quotation->expiry_date->format('d M Y') }}.
        </div>
    </div>
</body>
</html>
