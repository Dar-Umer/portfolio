<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Services\Audit\AuditService;
use App\Services\Common\DocumentNumberService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a verified payment and allocate it against an invoice safely.
     */
    public static function recordAndAllocate(Invoice $invoice, float $amount, string $paymentMethod, ?string $transactionRef = null, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($invoice, $amount, $paymentMethod, $transactionRef, $notes) {
            if ($amount <= 0) {
                throw new Exception("Payment amount must be greater than zero.");
            }

            if ($amount > (float) $invoice->balance_due) {
                throw new Exception("Payment amount (" . number_format($amount, 2) . ") exceeds invoice balance due (" . number_format($invoice->balance_due, 2) . ").");
            }

            $receiptNumber = DocumentNumberService::generate('RCT', 'payments', 'receipt_number');

            $payment = Payment::create([
                'receipt_number' => $receiptNumber,
                'client_profile_id' => $invoice->client_profile_id,
                'payment_date' => Carbon::now()->toDateString(),
                'amount' => $amount,
                'currency' => $invoice->currency,
                'payment_method' => $paymentMethod,
                'transaction_reference' => $transactionRef,
                'status' => 'completed',
                'notes' => $notes,
                'recorded_by_user_id' => auth()->id(),
            ]);

            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'amount_allocated' => $amount,
            ]);

            // Recompute invoice amounts
            $totalAllocated = (float) PaymentAllocation::where('invoice_id', $invoice->id)->sum('amount_allocated');
            $newBalanceDue = round((float) $invoice->grand_total - $totalAllocated, 2);

            $newStatus = $newBalanceDue <= 0 ? 'Paid' : 'Partially Paid';

            $invoice->update([
                'amount_paid' => $totalAllocated,
                'balance_due' => max(0, $newBalanceDue),
                'status' => $newStatus,
            ]);

            if ($invoice->project_id) {
                \App\Models\ProjectTimelineEvent::create([
                    'project_id' => $invoice->project_id,
                    'user_id' => auth()->id(),
                    'event_type' => 'payment_recorded',
                    'title' => "Payment Recorded: {$payment->receipt_number}",
                    'description' => "Received {$amount} {$invoice->currency} for invoice {$invoice->invoice_number}. Invoice status: {$newStatus}.",
                    'is_client_visible' => true,
                ]);
            }

            AuditService::log('payment_recorded', $payment, "Recorded payment {$payment->receipt_number} of {$amount} {$invoice->currency} for invoice {$invoice->invoice_number}");

            return $payment->load('allocations');
        });
    }
}
