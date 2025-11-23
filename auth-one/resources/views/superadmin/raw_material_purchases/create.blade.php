@extends('master')

@section('content')
<div class="container mt-4">
    <h4>Add Raw Material Purchase</h4>

    {{-- General Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        {{-- Display specific server-side errors --}}
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    {{-- Display ALL validation errors at the top for visibility --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.raw-material-purchases.store') }}" method="POST">
        @csrf

        {{-- INVOICE MASTER DATA --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Invoice Number</label>
                {{-- Use old() to retain value --}}
                <input type="text" name="invoice_number" class="form-control" required value="{{ old('invoice_number') }}">
                @error('invoice_number') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4">
                <label>Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" required value="{{ old('invoice_date', date('Y-m-d')) }}">
                @error('invoice_date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <table class="table table-bordered" id="itemsTable">
            <thead class="table-light">
                <tr>
                    <th>Existing Material</th>
                    <th>New Material Name</th>
                    <th>Unit</th>
                    <th>Batch</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- FIX: Loop through old items if available, otherwise show one default row --}}
                @php 
                    $items = old('items', [[]]); // At least one empty item if no old input
                    $rowIndex = 0;
                @endphp

                @foreach ($items as $index => $oldItem)
                <tr>
                    <td>
                        <select name="items[{{ $index }}][raw_material_id]" class="form-select raw-select">
                            <option value="">-- Select --</option>
                            @foreach($rawMaterials as $m)
                                <option 
                                    value="{{ $m->id }}" 
                                    data-unit="{{ $m->unit_of_measure }}"
                                    {{ (old("items.$index.raw_material_id") == $m->id) ? 'selected' : '' }}
                                >
                                    {{ $m->name }} ({{ $m->unit_of_measure }})
                                </option>
                            @endforeach
                        </select>
                        @error("items.$index.raw_material_id") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="text" name="items[{{ $index }}][new_material_name]" class="form-control" placeholder="If new" value="{{ old("items.$index.new_material_name") }}">
                        @error("items.$index.new_material_name") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="text" name="items[{{ $index }}][unit_of_measure]" class="form-control item-unit" placeholder="KG/Litre" value="{{ old("items.$index.unit_of_measure") }}">
                        @error("items.$index.unit_of_measure") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="text" name="items[{{ $index }}][batch_number]" class="form-control" value="{{ old("items.$index.batch_number") }}">
                        @error("items.$index.batch_number") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty" step="0.01" value="{{ old("items.$index.quantity") }}">
                        @error("items.$index.quantity") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control price" step="0.01" value="{{ old("items.$index.unit_price") }}">
                        @error("items.$index.unit_price") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][total_price]" class="form-control total" readonly value="{{ old("items.$index.total_price") }}">
                        @error("items.$index.total_price") <small class="text-danger">{{ $message }}</small> @enderror
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                </tr>
                @php $rowIndex++; @endphp
                @endforeach
            </tbody>
        </table>

        <button type="button" id="addRow" class="btn btn-secondary mb-3">+ Add Item</button>

        {{-- SUMMARY AND NOTES --}}
        <div class="row mb-3">
            {{-- FIX: Added missing Notes field --}}
            <div class="col-md-7">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            
            <div class="col-md-4 ms-auto">
                <label>Discount</label>
                <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" value="{{ old('discount_amount', 0) }}">
                @error('discount_amount') <small class="text-danger">{{ $message }}</small> @enderror
                
                <label>Grand Total</label>
                {{-- FIX: Set initial value of grand total from old() --}}
                <input type="number" name="grand_total" id="grand_total" class="form-control" step="0.01" readonly value="{{ old('grand_total') }}">
                @error('grand_total') <small class="text-danger">{{ $message }}</small> @enderror
                
                <input type="hidden" name="calculated_total" id="calculated_total" value="{{ old('calculated_total') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Purchase</button>
    </form>
</div>

<script>
let row = {{ $rowIndex }}; // Start row index after any existing old rows

// Function to handle unit of measure auto-fill
function handleUnitSelection(selectElement) {
    const tr = selectElement.closest('tr');
    const unitInput = tr.querySelector('.item-unit');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    // Clear New Material Name if an existing one is selected
    if (selectElement.value) {
        tr.querySelector('[name*="new_material_name"]').value = '';
    }

    if (selectedOption && selectedOption.dataset.unit) {
        unitInput.value = selectedOption.dataset.unit;
    }
}

// Attach listener for initial unit selection (for old data)
document.querySelectorAll('.raw-select').forEach(select => {
    handleUnitSelection(select);
});

// Add new row
document.getElementById('addRow').addEventListener('click', () => {
    const tbody = document.querySelector('#itemsTable tbody');
    // Get the structure of the last row for cloning
    const lastRow = tbody.lastElementChild;
    const tr = lastRow.cloneNode(true);
    
    tr.querySelectorAll('input, select').forEach(input => {
        // Clear all values
        if (input.type === 'text' || input.type === 'number') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }

        // Update name index
        let name = input.name.replace(/items\[\d+\]/, `items[${row}]`);
        input.name = name;
    });
    tbody.appendChild(tr);
    row++;
});

// Remove row
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('removeRow')) {
        if(document.querySelectorAll('#itemsTable tbody tr').length > 1)
            e.target.closest('tr').remove();
        calculateGrandTotal(); // Recalculate after removal
    }
});

// Main input listener for calculations
document.addEventListener('input', function(e) {
    const target = e.target;
    
    if (target.classList.contains('raw-select')) {
        handleUnitSelection(target);
    }
    
    if(target.classList.contains('qty') || target.classList.contains('price')) {
        const tr = target.closest('tr');
        const qty = parseFloat(tr.querySelector('.qty').value) || 0;
        const price = parseFloat(tr.querySelector('.price').value) || 0;
        const total = qty * price;
        tr.querySelector('.total').value = total.toFixed(2);
        calculateGrandTotal();
    } else if (target.id === 'discount_amount') {
        calculateGrandTotal();
    }
});

// FIX: Ensure discount is factored into the final total
function calculateGrandTotal() {
    let subTotal = 0;
    document.querySelectorAll('.total').forEach(el => subTotal += parseFloat(el.value) || 0);
    
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    
    let grandTotal = subTotal - discount;
    
    // Set Grand Total and Calculated Total with 2 decimal precision
    document.getElementById('grand_total').value = grandTotal.toFixed(2);
    document.getElementById('calculated_total').value = grandTotal.toFixed(2);
}

// Initial calculation on page load (for old data)
document.addEventListener('DOMContentLoaded', calculateGrandTotal);
</script>
@endsection