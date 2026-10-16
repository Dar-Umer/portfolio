@extends('layouts.admin')

@section('page_title', 'Create New Quotation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Quotations
            </a>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Create Commercial Quotation</h5>
                    <p class="text-muted small mb-0">Specify client profile, deliverables schedule, itemized costs, and tax breakdown.</p>
                </div>
            </div>

            <div class="card-body p-4">
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2 small mb-4">
                        <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please resolve the following errors:</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.quotations.store') }}">
                    @csrf

                    <!-- Client & Project Link -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Client Association & Scope
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Target Client <span class="text-danger">*</span></label>
                            <select name="client_profile_id" class="form-select" required>
                                <option value="">Select Client...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_profile_id', request('client_id')) == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name ?? $c->user->name }} ({{ $c->client_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Linked Project (Optional)</label>
                            <select name="project_id" class="form-select">
                                <option value="">Standalone Scope / Proposal</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id', request('project_id')) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->project_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Quotation Dates & Currency -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-calendar-check text-warning"></i> Validity & Pricing Currency
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Offer Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label small fw-semibold mb-1">Currency <span class="text-danger">*</span></label>
                                <span class="text-muted small" style="font-size: 11px;" id="rate-badge">1 USD ≈ ₹85.00</span>
                            </div>
                            <select name="currency" id="currency-select" class="form-select" required>
                                <option value="INR" selected>INR (₹) - Indian Rupee</option>
                                <option value="USD">USD ($) - US Dollar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                            <i class="fas fa-list-check text-success"></i> Billable Deliverables & Line Items
                        </div>
                        <div class="d-flex gap-2">
                            @if(isset($services) && $services->count() > 0)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="servicesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-magic me-1"></i> Quick Insert Service
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="servicesDropdown">
                                        @foreach($services as $svc)
                                            <li>
                                                <a class="dropdown-item py-2 insert-service-btn" href="#" 
                                                   data-title="{{ $svc->title }}" 
                                                   data-price="{{ $svc->starting_price ?? 0 }}">
                                                    <span class="fw-semibold text-dark">{{ $svc->title }}</span>
                                                    <div class="small text-muted">₹{{ number_format($svc->starting_price, 2) }}</div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <button type="button" id="add-item-btn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle bg-white" id="items-table">
                            <thead class="table-light small text-uppercase fw-semibold" style="font-size: 11px;">
                                <tr>
                                    <th style="min-width: 250px;">Deliverable / Service Description <span class="text-danger">*</span></th>
                                    <th style="width: 90px;">Qty <span class="text-danger">*</span></th>
                                    <th style="width: 140px;">Unit Price (<span class="currency-symbol">₹</span>) <span class="text-danger">*</span></th>
                                    <th style="width: 110px;">Disc (<span class="currency-symbol">₹</span>)</th>
                                    <th style="width: 100px;">Tax (%)</th>
                                    <th style="width: 140px;" class="text-end">Line Total (<span class="currency-symbol">₹</span>)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-container">
                                <!-- Dynamic rows inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Card -->
                    <div class="row justify-content-end mb-4">
                        <div class="col-md-5">
                            <div class="card bg-light border-0 shadow-sm p-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Subtotal:</span>
                                    <span class="fw-semibold font-monospace" id="summary-subtotal">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Total Discount:</span>
                                    <span class="text-danger font-monospace" id="summary-discount">-₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">Taxes Total:</span>
                                    <span class="fw-semibold font-monospace" id="summary-tax">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2">
                                    <span class="fw-bold text-dark">Grand Total:</span>
                                    <span class="fw-bold text-primary font-monospace fs-5" id="summary-grand">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Client Scope Notes</label>
                        <textarea name="client_notes" class="form-control" rows="2" placeholder="Specific technical requirements or notes for the customer...">{{ old('client_notes') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Commercial Terms & Conditions</label>
                        <textarea name="terms_and_conditions" class="form-control" rows="2">50% Advance on project kickoff, 50% upon deployment and delivery approval. Valid for 30 calendar days.</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Generate Quotation Proposal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;
    const container = document.getElementById('items-container');
    const addItemBtn = document.getElementById('add-item-btn');
    const currencySelect = document.getElementById('currency-select');
    const USD_INR_RATE = 85.00; // 1 USD = 85 INR

    let currentCurrency = currencySelect.value; // 'INR' or 'USD'

    function getCurrencySymbol(curr) {
        return curr === 'USD' ? '$' : '₹';
    }

    function updateCurrencySymbols() {
        const symbol = getCurrencySymbol(currentCurrency);
        document.querySelectorAll('.currency-symbol').forEach(el => {
            el.innerText = symbol;
        });
    }

    function createRow(description = '', qty = 1, unitPrice = 0.00, discount = 0.00, taxRate = 18.00) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = itemIndex;

        const symbol = getCurrencySymbol(currentCurrency);

        tr.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm item-desc" value="${description}" placeholder="e.g. Custom Web Application Development or Custom item" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="items[${itemIndex}][quantity]" class="form-control form-control-sm font-monospace item-qty text-center" value="${qty}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm font-monospace item-price" value="${unitPrice}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${itemIndex}][discount]" class="form-control form-control-sm font-monospace item-discount" value="${discount}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${itemIndex}][tax_rate]" class="form-control form-control-sm font-monospace item-tax" value="${taxRate}">
            </td>
            <td class="text-end font-monospace fw-semibold small text-dark item-line-total">
                ${symbol}0.00
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn" title="Remove item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(tr);
        itemIndex++;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let discountTotal = 0;
        let taxTotal = 0;
        const symbol = getCurrencySymbol(currentCurrency);

        const rows = container.querySelectorAll('.item-row');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

            const lineSub = qty * price;
            const lineTaxable = Math.max(0, lineSub - discount);
            const lineTax = lineTaxable * (taxRate / 100);
            const lineTotal = lineTaxable + lineTax;

            row.querySelector('.item-line-total').innerText = symbol + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            subtotal += lineSub;
            discountTotal += discount;
            taxTotal += lineTax;
        });

        const grandTotal = Math.max(0, (subtotal - discountTotal) + taxTotal);

        document.getElementById('summary-subtotal').innerText = symbol + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('summary-discount').innerText = '-' + symbol + discountTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('summary-tax').innerText = symbol + taxTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('summary-grand').innerText = symbol + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Currency Switch Handler: converts existing unit_price & discount between INR and USD
    currencySelect.addEventListener('change', function () {
        const newCurrency = this.value;
        if (newCurrency === currentCurrency) return;

        const rows = container.querySelectorAll('.item-row');
        rows.forEach(row => {
            const priceInput = row.querySelector('.item-price');
            const discountInput = row.querySelector('.item-discount');

            let price = parseFloat(priceInput.value) || 0;
            let discount = parseFloat(discountInput.value) || 0;

            if (newCurrency === 'USD' && currentCurrency === 'INR') {
                // Converting from INR to USD
                priceInput.value = (price / USD_INR_RATE).toFixed(2);
                discountInput.value = (discount / USD_INR_RATE).toFixed(2);
            } else if (newCurrency === 'INR' && currentCurrency === 'USD') {
                // Converting from USD to INR
                priceInput.value = Math.round(price * USD_INR_RATE).toFixed(2);
                discountInput.value = Math.round(discount * USD_INR_RATE).toFixed(2);
            }
        });

        currentCurrency = newCurrency;
        updateCurrencySymbols();
        calculateTotals();
    });

    // Attach row events
    container.addEventListener('input', function (e) {
        if (e.target.matches('.item-qty, .item-price, .item-discount, .item-tax')) {
            calculateTotals();
        }
    });

    container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-item-btn');
        if (removeBtn) {
            const rows = container.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('At least one line item is required in the quotation.');
                return;
            }
            removeBtn.closest('tr').remove();
            calculateTotals();
        }
    });

    addItemBtn.addEventListener('click', function () {
        createRow('', 1, 0.00, 0.00, 18.00);
    });

    // Quick insert service from dropdown
    document.querySelectorAll('.insert-service-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const title = this.dataset.title;
            let price = parseFloat(this.dataset.price) || 0;

            // If current currency is USD, convert the starting catalog price from INR to USD
            if (currentCurrency === 'USD') {
                price = +(price / USD_INR_RATE).toFixed(2);
            }

            // If the first row is empty, fill it instead of adding a new one
            const rows = container.querySelectorAll('.item-row');
            if (rows.length === 1 && rows[0].querySelector('.item-desc').value.trim() === '' && parseFloat(rows[0].querySelector('.item-price').value) === 0) {
                rows[0].querySelector('.item-desc').value = title;
                rows[0].querySelector('.item-price').value = price.toFixed(2);
                calculateTotals();
            } else {
                createRow(title, 1, price.toFixed(2), 0.00, 18.00);
            }
        });
    });

    // Initial load
    updateCurrencySymbols();
    createRow('Custom Web Application Development', 1, 25000.00, 0.00, 18.00);
});
</script>
@endsection
