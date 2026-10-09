<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; }
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
    <div class="invoice-box">
        <table class="header" width="100%">
            <tr>
                <td>
                    <div class="company-title">The Umer Store</div>
                    <div>Software Systems & Enterprise Architectures</div>
                    <div>GSTIN: 09ABCDE1234F1Z5 &bull; State: Uttar Pradesh</div>
                </td>
                <td align="right">
                    <div class="doc-title">TAX INVOICE</div>
                    <div><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $invoice->issue_date->format('d M Y') }}</div>
                    <div><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
                    <div><strong>Status:</strong> {{ $invoice->status }}</div>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td width="55%">
                    <strong>Billed To:</strong><br>
                    {{ $invoice->client_snapshot['company_name'] ?? $invoice->client->company_name }}<br>
                    Attn: {{ $invoice->client_snapshot['name'] ?? $invoice->client->user->name }}<br>
                    {{ $invoice->client_snapshot['billing_address'] ?? $invoice->client->billing_address }}<br>
                    {{ $invoice->client_snapshot['city'] ?? $invoice->client->city }}, {{ $invoice->client_snapshot['state'] ?? $invoice->client->state }} - {{ $invoice->client_snapshot['postal_code'] ?? $invoice->client->postal_code }}<br>
                    GSTIN / Tax ID: {{ $invoice->client_snapshot['tax_number'] ?? ($invoice->client->tax_number ?? 'N/A') }}
                </td>
                <td width="45%">
                    <strong>Project Reference:</strong><br>
                    {{ $invoice->project->name ?? 'Enterprise Engineering Services' }}<br>
                    @if($invoice->quotation)
                        Originated from: {{ $invoice->quotation->quotation_number }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Description</th>
                    <th width="12%">Qty</th>
                    <th width="18%">Unit Price</th>
                    <th width="20%" align="right">Total ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
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
                <td align="right">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td align="right">-{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->cgst_amount > 0)
                <tr>
                    <td>CGST:</td>
                    <td align="right">{{ number_format($invoice->cgst_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST:</td>
                    <td align="right">{{ number_format($invoice->sgst_amount, 2) }}</td>
                </tr>
            @elseif($invoice->igst_amount > 0)
                <tr>
                    <td>IGST:</td>
                    <td align="right">{{ number_format($invoice->igst_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td align="right">{{ $invoice->currency }} {{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td align="right">{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td align="right"><strong>{{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</strong></td>
            </tr>
        </table>

        @if($invoice->payment_terms)
            <div style="font-size: 11px; margin-top: 20px;">
                <strong>Payment Terms:</strong> {{ $invoice->payment_terms }}
            </div>
        @endif

        <div class="footer">
            Computer generated tax invoice. Electronic signature not required. Contact: contact@theumer.store
        </div>
    </div>
</body>
</html>
