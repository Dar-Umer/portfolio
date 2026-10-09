<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Services\Audit\AuditService;
use App\Services\Common\DocumentNumberService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Convert an accepted Quotation into an immutable Invoice with GST calculation.
     */
    public static function convertQuotationToInvoice(Quotation $quotation, array $customDetails = []): Invoice
    {
        return DB::transaction(function () use ($quotation, $customDetails) {
            if ($quotation->status !== 'Accepted') {
                throw new Exception("Only Accepted quotations can be converted to an invoice.");
            }

            if ($quotation->invoice()->exists()) {
                throw new Exception("Quotation {$quotation->quotation_number} has already been converted to an invoice.");
            }

            $client = $quotation->client;
            if (!$client) {
                throw new Exception("Associated client profile not found.");
            }

            // Create client snapshot at issuance
            $clientSnapshot = [
                'client_number' => $client->client_number,
                'name' => $client->user?->name,
                'email' => $client->user?->email,
                'company_name' => $client->company_name,
                'phone' => $client->phone,
                'billing_address' => $client->billing_address,
                'city' => $client->city,
                'state' => $client->state,
                'postal_code' => $client->postal_code,
                'country' => $client->country,
                'tax_number' => $client->tax_number,
            ];

            $invoiceNumber = DocumentNumberService::generate('INV', 'invoices', 'invoice_number');
            $issueDate = $customDetails['issue_date'] ?? Carbon::now()->toDateString();
            $dueDate = $customDetails['due_date'] ?? Carbon::now()->addDays(15)->toDateString();

            // Calculate GST based on Client State vs Seller State (configurable default India/same state)
            $isSameState = strcasecmp((string)$client->state, (string)config('app.seller_state', 'Uttar Pradesh')) === 0;

            $taxTotal = (float) $quotation->tax_total;
            $cgst = 0.00;
            $sgst = 0.00;
            $igst = 0.00;

            if ($taxTotal > 0) {
                if ($isSameState) {
                    $cgst = round($taxTotal / 2, 2);
                    $sgst = round($taxTotal - $cgst, 2);
                } else {
                    $igst = $taxTotal;
                }
            }

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_profile_id' => $client->id,
                'project_id' => $quotation->project_id,
                'quotation_id' => $quotation->id,
                'created_by_user_id' => auth()->id(),
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'status' => 'Issued',
                'currency' => $quotation->currency,
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'taxable_amount' => round((float)$quotation->subtotal - (float)$quotation->discount_amount, 2),
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_total' => $taxTotal,
                'grand_total' => $quotation->grand_total,
                'amount_paid' => 0.00,
                'balance_due' => $quotation->grand_total,
                'client_snapshot' => $clientSnapshot,
                'client_notes' => $quotation->client_notes,
                'payment_terms' => $customDetails['payment_terms'] ?? 'Due upon receipt or within 15 days.',
            ]);

            // Copy line items
            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'tax_rate' => $item->tax_rate,
                    'total' => $item->total,
                    'sort_order' => $item->sort_order,
                ]);
            }

            // Update quotation status
            $quotation->update(['status' => 'Converted to Invoice']);

            AuditService::log('convert_quotation_to_invoice', $invoice, "Converted quotation {$quotation->quotation_number} into invoice {$invoice->invoice_number}");

            return $invoice->load(['items', 'client']);
        });
    }
}
