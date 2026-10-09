<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Services\Audit\AuditService;
use App\Services\Billing\BillingService;
use App\Services\Common\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quotation::with(['client.user', 'project', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.quotations.index', compact('quotations'));
    }

    public function create(): View
    {
        $clients = ClientProfile::with('user')->where('account_status', 'active')->get();
        $projects = Project::orderBy('name')->get();

        return view('admin.quotations.create', compact('clients', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_profile_id' => ['required', 'exists:client_profiles,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'currency' => ['required', 'string', 'max:10'],
            'client_notes' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $quotationNumber = DocumentNumberService::generate('QTN', 'quotations', 'quotation_number');

        $subtotal = 0.00;
        $discountAmount = 0.00;
        $taxTotal = 0.00;

        foreach ($validated['items'] as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDisc = (float) ($item['discount'] ?? 0);
            $taxRate = (float) ($item['tax_rate'] ?? 0);

            $itemSub = $qty * $price;
            $itemTaxable = max(0, $itemSub - $itemDisc);
            $itemTax = round($itemTaxable * ($taxRate / 100), 2);

            $subtotal += $itemSub;
            $discountAmount += $itemDisc;
            $taxTotal += $itemTax;
        }

        $grandTotal = round(($subtotal - $discountAmount) + $taxTotal, 2);

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'client_profile_id' => $validated['client_profile_id'],
            'project_id' => $validated['project_id'] ?? null,
            'created_by_user_id' => auth()->id(),
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'status' => 'Sent',
            'currency' => $validated['currency'],
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
            'client_notes' => $validated['client_notes'] ?? null,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
        ]);

        foreach ($validated['items'] as $index => $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $itemDisc = (float) ($item['discount'] ?? 0);
            $taxRate = (float) ($item['tax_rate'] ?? 0);
            $lineTotal = round(($qty * $price - $itemDisc) * (1 + $taxRate / 100), 2);

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $qty,
                'unit_price' => $price,
                'discount' => $itemDisc,
                'tax_rate' => $taxRate,
                'total' => $lineTotal,
                'sort_order' => $index + 1,
            ]);
        }

        AuditService::log('quotation_created', $quotation, "Created quotation {$quotation->quotation_number}");

        return redirect()->route('admin.quotations.show', $quotation)->with('success', "Quotation {$quotation->quotation_number} generated.");
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['client.user', 'project', 'items', 'invoice']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function convert(Quotation $quotation): RedirectResponse
    {
        try {
            // If quotation is Sent, mark accepted for conversion
            if ($quotation->status === 'Sent') {
                $quotation->update([
                    'status' => 'Accepted',
                    'accepted_at' => now(),
                    'accepted_by_user_id' => auth()->id(),
                ]);
            }

            $invoice = BillingService::convertQuotationToInvoice($quotation);

            return redirect()->route('admin.invoices.show', $invoice)->with('success', "Quotation {$quotation->quotation_number} converted to Invoice {$invoice->invoice_number}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPdf(Quotation $quotation): Response
    {
        $quotation->load(['client.user', 'project', 'items']);
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));

        return $pdf->download("{$quotation->quotation_number}.pdf");
    }
}
