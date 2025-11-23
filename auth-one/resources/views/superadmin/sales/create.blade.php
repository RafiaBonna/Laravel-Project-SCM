@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">➕ New Sales Invoice</h3>
        </div>

        <form action="{{ route('superadmin.sales.store') }}" method="POST">
            @csrf
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Invoice Basic Info --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Invoice No <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_no" class="form-control" 
                               value="{{ old('invoice_no', $invoice_no) }}" readonly required>
                    </div>

                    <div class="col-md-4">
                        <label>Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control" 
                               value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label>Select Depo <span class="text-danger">*</span></label>
                        <select name="depo_id" class="form-select" required>
                            <option value="">-- Select Depo --</option>
                            @foreach ($depos as $depo)
                                <option value="{{ $depo->id }}" {{ old('depo_id') == $depo->id ? 'selected' : '' }}>
                                    {{ $depo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Product Items Table --}}
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Product Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover table-sm mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 40%">Product <span class="text-danger">*</span></th>
                                    <th style="width: 15%">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 20%">Unit Price <span class="text-danger">*</span></th>
                                    <th style="width: 15%">Subtotal</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="item-table-body">
                                {{-- JS dynamic rows --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                    <td class="text-center fw-bold">
                                        <input type="text" id="grand_total" class="form-control form-control-sm text-center fw-bold" value="0.00" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">
                                        <button type="button" class="btn btn-sm btn-success" id="add-item-row">
                                            <i class="fas fa-plus"></i> Add Product
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary btn-lg">Save Invoice</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Hidden Row Template --}}
<table class="d-none">
    <tr id="item-row-template">
        <td>
            <select name="items[__INDEX__][product_id]" class="form-select form-select-sm product-select" required>
                <option value="">-- Select Product --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[__INDEX__][quantity]" class="form-control form-control-sm quantity text-center" min="1" value="1" required></td>
        <td><input type="number" name="items[__INDEX__][unit_price]" class="form-control form-control-sm unit-price text-end" min="0" step="0.01" value="0.00" required></td>
        <td class="text-center fw-bold subtotal-text">0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
    </tr>
</table>

@endsection

@section('scripts')
<script>
let itemIndex = 0;

$(document).ready(function() {
    addItemRow();

    $('#add-item-row').on('click', function() {
        addItemRow();
    });

    $('#item-table-body').on('input', '.quantity, .unit-price', function() {
        let row = $(this).closest('tr');
        updateRowSubtotal(row);
        updateGrandTotal();
    });

    $('#item-table-body').on('click', '.remove-row', function() {
        if ($('#item-table-body tr').length > 1) {
            $(this).closest('tr').remove();
            updateGrandTotal();
        } else {
            alert('At least one product is required.');
        }
    });
});

function addItemRow() {
    let newRow = $('#item-row-template').clone();
    let rowHtml = newRow.html().replace(/__INDEX__/g, itemIndex);
    newRow.html(rowHtml);
    newRow.removeAttr('id').show();
    $('#item-table-body').append(newRow);
    updateGrandTotal();
    itemIndex++;
}

function updateRowSubtotal(row) {
    let qty = parseFloat(row.find('.quantity').val()) || 0;
    let price = parseFloat(row.find('.unit-price').val()) || 0;
    let subtotal = qty * price;
    row.find('.subtotal-text').text(subtotal.toFixed(2));
}

function updateGrandTotal() {
    let grandTotal = 0;
    $('#item-table-body tr').each(function() {
        let subtotal = parseFloat($(this).find('.subtotal-text').text()) || 0;
        grandTotal += subtotal;
    });
    $('#grand_total').val(grandTotal.toFixed(2));
}
</script>
@endsection
