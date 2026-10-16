<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\Audit\AuditService;
use App\Services\Payments\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['client.user', 'project', 'allocations']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client.user', 'project', 'items', 'allocations.payment']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $payment = PaymentService::recordAndAllocate(
                $invoice,
                (float) $validated['amount'],
                $validated['payment_method'],
                $validated['transaction_reference'],
                $validated['notes']
            );

            return back()->with('success', "Payment {$payment->receipt_number} recorded successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $invoice->load(['client.user', 'project', 'items']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->download("{$invoice->invoice_number}.pdf");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoiceNumber = $invoice->invoice_number;

        // If payments are allocated, disallow or handle cleanly
        if ($invoice->allocations()->exists()) {
            return back()->with('error', "Cannot delete {$invoiceNumber} because payments have already been allocated to it. Revert payment allocations first.");
        }

        $quotation = $invoice->quotation;

        $invoice->items()->delete();
        $invoice->forceDelete();

        if ($quotation) {
            $quotation->update([
                'status' => 'Accepted',
            ]);
        }

        AuditService::log('invoice_deleted', null, "Deleted tax invoice {$invoiceNumber}");

        return redirect()->route('admin.invoices.index')->with('success', "Invoice {$invoiceNumber} was deleted successfully.");
    }
}
