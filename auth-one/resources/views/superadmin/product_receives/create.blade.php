@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">➕ New Product Receive Entry</h3>
        </div>

        <form action="{{ route('superadmin.product-receives.store') }}" method="POST">
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
                
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Receive No <span class="text-danger">*</span></label>
                        {{-- Only one option, so use text input or hidden field if you don't want a dropdown --}}
                        <input type="text" name="receive_no" class="form-control" value="{{ $receiveNos[0] ?? '' }}"  required>
                        @error('receive_no')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Receive Date <span class="text-danger">*</span></label>
                        <input type="date" name="receive_date" class="form-control @error('receive_date') is-invalid @enderror" value="{{ old('receive_date', date('Y-m-d')) }}" required>
                        @error('receive_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Receiver <span class="text-danger">*</span></label>
                        <select name="receiver_id" class="form-control @error('receiver_id') is-invalid @enderror" required>
                            <option value="">Select Receiver</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ old('receiver_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('receiver_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                    </div>
                </div>

                <h5 class="mt-4">Product Items</h5>
                {{-- Show error if no items are added --}}
                @error('items')<span class="text-danger">You must add at least one item.</span>@enderror

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Product <span class="text-danger">*</span></th>
                                <th>Batch No</th>
                                <th>Qty <span class="text-danger">*</span></th>
                                <th>MRP</th>
                                <th>Retail</th>
                                <th>Distributor</th>
                                <th>Depo Selling</th>
                                <th>Cost Rate</th>
                                <th>Production Date</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="item-table-body">
                            {{-- Initial Row --}}
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.0.product_id')<span class="text-danger">{{ $message }}</span>@enderror
                                </td>
                                <td><input type="text" name="items[0][batch_no]" class="form-control"></td>
                                {{-- **FIX: Added required attribute** --}}
                                <td><input type="number" step="0.01" name="items[0][qty]" class="form-control received-qty" required>
                                    @error('items.0.qty')<span class="text-danger">{{ $message }}</span>@enderror
                                </td>
                                <td><input type="number" step="0.01" name="items[0][mrp]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="items[0][retail]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="items[0][distributor]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="items[0][depo_selling]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="items[0][cost_rate]" class="form-control cost-rate"></td>
                                <td><input type="date" name="items[0][production_date]" class="form-control"></td>
                                <td><input type="date" name="items[0][expiry_date]" class="form-control"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11" class="text-right">
                                    <button type="button" id="addNewItem" class="btn btn-sm btn-success">+ Add Item</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                {{-- These inputs are for display only, removed 'readonly' and 'name' attributes as they are calculated in controller --}}
                <div class="form-group text-right mt-2">
                    <label>Total Received Qty</label>
                    <input type="text" id="total_received_qty" class="form-control text-right" value="0.00" readonly>
                </div>
                <div class="form-group text-right mt-2">
                    <label>Total Cost</label>
                    <input type="text" id="total_cost" class="form-control text-right" value="0.00" readonly>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Receive Entry</button>
            </div>
        </form>

        <table style="display:none;">
            <tbody>
                <tr id="item-row-template">
                    <td>
                        <select name="items[__INDEX__][product_id]" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="items[__INDEX__][batch_no]" class="form-control"></td>
                    {{-- **FIX: Added required attribute** --}}
                    <td><input type="number" step="0.01" name="items[__INDEX__][qty]" class="form-control received-qty" required></td>
                    <td><input type="number" step="0.01" name="items[__INDEX__][mrp]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="items[__INDEX__][retail]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="items[__INDEX__][distributor]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="items[__INDEX__][depo_selling]" class="form-control"></td>
                    <td><input type="number" step="0.01" name="items[__INDEX__][cost_rate]" class="form-control cost-rate"></td>
                    <td><input type="date" name="items[__INDEX__][production_date]" class="form-control"></td>
                    <td><input type="date" name="items[__INDEX__][expiry_date]" class="form-control"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
let itemIndex = 1;

$('#addNewItem').click(function() {
    let template = $('#item-row-template').prop('outerHTML');
    // Important: Use .html() to get the content of the <tr> and append it directly
    // Also, ensure the template is correctly cloned and indices updated
    let newRow = $('#item-row-template').clone();
    
    // Replace the placeholder __INDEX__ with the current item index
    newRow.html(newRow.html().replace(/__INDEX__/g, itemIndex));
    newRow.removeAttr('id').show(); // Remove ID from the cloned row and ensure it's visible
    
    $('#item-table-body').append(newRow);
    itemIndex++;
    updateTotals();
});

// Initial row removal is also important
$(document).on('click', '.remove-row', function(){
    // Ensure at least one row remains if necessary, or check count
    if ($('#item-table-body tr').length > 1) {
        $(this).closest('tr').remove();
    } else {
        alert('You must have at least one product item.');
    }
    updateTotals();
});

function updateTotals() {
    let totalQty = 0;
    let totalCost = 0;

    $('#item-table-body tr').each(function() {
        let qty = parseFloat($(this).find('.received-qty').val()) || 0;
        let costRate = parseFloat($(this).find('.cost-rate').val()) || 0;
        totalQty += qty;
        totalCost += qty * costRate;
    });

    $('#total_received_qty').val(totalQty.toFixed(2));
    $('#total_cost').val(totalCost.toFixed(2));
}

// Recalculate totals whenever qty or cost rate changes
$(document).on('input', '.received-qty, .cost-rate', updateTotals);

// Initialize totals on page load
$(document).ready(function() {
    updateTotals(); 
});
</script>
@endsection