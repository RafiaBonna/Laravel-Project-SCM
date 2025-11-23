@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">✏️ Edit Product Receive Entry</h3>
        </div>

        <form action="{{ route('superadmin.product-receives.update', $receive->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Receive No</label>
                        <input type="text" name="receive_no" class="form-control" value="{{ $receive->receive_no }}" readonly>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Receive Date <span class="text-danger">*</span></label>
                        <input type="date" name="receive_date" class="form-control" value="{{ $receive->receive_date }}" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Receiver <span class="text-danger">*</span></label>
                        <select name="receiver_id" class="form-control" required>
                            <option value="">-- Select Receiver --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ $receive->receiver_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control" value="{{ $receive->note }}">
                    </div>
                </div>

                <h5 class="mt-4">Product Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Qty</th>
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
                            @foreach($receive->items as $i => $item)
                                @include('superadmin.product_receives.partials.receive_item_row', ['i'=>$i, 'products'=>$products, 'item'=>$item])
                            @endforeach
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

                <div class="form-group text-right mt-2">
                    <label>Total Received Qty</label>
                    <input type="text" name="total_received_qty" id="total_received_qty" class="form-control text-right" value="{{ $receive->items->sum('qty') }}" readonly>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update Receive Entry</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let itemIndex = {{ $receive->items->count() - 1 }};

    function updateTotalQty() {
        let total = 0;
        $('.received-qty').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_received_qty').val(total.toFixed(2));
    }

    $(document).on('input', '.received-qty', updateTotalQty);

    $('#addNewItem').click(function() {
        itemIndex++;
        $.get('{{ route("superadmin.product-receives.get-item-row") }}', { i: itemIndex }, function(html){
            $('#item-table-body').append(html);
        });
    });

    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
        updateTotalQty();
    });
</script>
@endsection
