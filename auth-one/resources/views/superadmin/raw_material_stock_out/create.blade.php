@extends('master')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">➕ Add Raw Material Stock Out</h4>

    {{-- Success / Error Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('superadmin.raw-material-stock-out.store') }}" method="POST" id="stockOutForm">
        @csrf

        {{-- Issue Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Issue Slip No <span class="text-danger">*</span></label>
                        <input type="text" name="slip_number" class="form-control" placeholder="Enter slip no" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Factory Name</label>
                        <input type="text" name="factory_name" class="form-control" placeholder="Factory name (optional)">
                    </div>
                    <div class="col-md-12 mt-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Enter notes (optional)"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Stock Out Items</h5>
                <button type="button" class="btn btn-sm btn-success" id="addRowBtn">+ Add Row</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 25%">Material</th>
                            <th style="width: 20%">Batch</th>
                            <th style="width: 15%">Qty</th>
                            <th style="width: 15%">Unit Price</th>
                            <th style="width: 15%">Total</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][raw_material_id]" class="form-control raw-material-select" required>
                                    <option value="">Select Material</option>
                                    @foreach($rawMaterials as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit_of_measure }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[0][raw_material_stock_id]" class="form-control batch-select" disabled required>
                                    <option value="">Select Batch</option>
                                </select>
                                <input type="hidden" name="items[0][batch_number]" class="batch-number-input">
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control quantity-input" step="0.01" placeholder="Qty" required></td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control unit-price-input" step="0.01" placeholder="Unit Price" required></td>
                            <td><input type="number" class="form-control total-input" step="0.01" placeholder="Total" readonly></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRowBtn">&times;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-5">Save Issue</button>
        </div>
    </form>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;

    // Add New Row
    document.getElementById('addRowBtn').addEventListener('click', function () {
        const table = document.querySelector('#itemsTable tbody');
        const newRow = table.rows[0].cloneNode(true);

        // Update name attributes
        newRow.querySelectorAll('select, input').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\d+/, rowIndex));
            }
            el.value = '';
        });

        table.appendChild(newRow);
        rowIndex++;
    });

    // Remove Row
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRowBtn')) {
            const table = document.querySelector('#itemsTable tbody');
            if (table.rows.length > 1) e.target.closest('tr').remove();
        }
    });

    // Material Change → Fetch Batches
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('raw-material-select')) {
            const row = e.target.closest('tr');
            const materialId = e.target.value;
            const batchSelect = row.querySelector('.batch-select');
            const batchNumberInput = row.querySelector('.batch-number-input');
            const unitPriceInput = row.querySelector('.unit-price-input');
            const qtyInput = row.querySelector('.quantity-input');

            batchSelect.innerHTML = '<option>Loading...</option>';
            batchSelect.disabled = true;
            unitPriceInput.value = '';
            qtyInput.value = '';
            batchNumberInput.value = '';

            if (!materialId) return;

            // ✅ FIX: 404 Error Fix - Using Laravel URL helper to prevent 404
            fetch(`{{ url('superadmin/api/raw-material-stock/batches') }}/${materialId}`)
                .then(res => res.json())
                .then(batches => {
                    if (batches.length === 0) {
                        batchSelect.innerHTML = '<option value="">No stock available</option>';
                        batchSelect.disabled = true;
                    } else {
                        let opts = '<option value="">Select Batch</option>';
                        batches.forEach(b => {
                            opts += `<option value="${b.id}" data-batch="${b.batch_number}" data-price="${b.average_purchase_price}" data-qty="${b.stock_quantity}">
                                ${b.batch_number} (Qty: ${b.stock_quantity})
                            </option>`;
                        });
                        batchSelect.innerHTML = opts;
                        batchSelect.disabled = false;
                    }
                })
                .catch(() => {
                    batchSelect.innerHTML = '<option>Error loading</option>';
                });
        }
    });

    // Batch Change → Fill Price
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('batch-select')) {
            const row = e.target.closest('tr');
            const selected = e.target.selectedOptions[0];
            const priceInput = row.querySelector('.unit-price-input');
            const batchNumberInput = row.querySelector('.batch-number-input');
            const qtyInput = row.querySelector('.quantity-input');

            if (selected && selected.value) {
                priceInput.value = selected.dataset.price;
                batchNumberInput.value = selected.dataset.batch;
                qtyInput.placeholder = `Available: ${selected.dataset.qty}`;
            } else {
                priceInput.value = '';
                batchNumberInput.value = '';
                qtyInput.placeholder = '';
            }
        }
    });

    // Quantity × Price = Total
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            const totalInput = row.querySelector('.total-input');
            totalInput.value = (qty * price).toFixed(2);
        }
    });
});
</script>
@endsection