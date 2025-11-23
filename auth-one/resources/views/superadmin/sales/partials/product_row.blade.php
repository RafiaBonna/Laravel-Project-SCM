<tr>
    <td>
        <select name="items[{{ $rowId }}][product_id]" class="form-control product-select" required>
            <option value="">Select Product</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="items[{{ $rowId }}][product_stock_id]" class="form-control batch-select" required disabled>
            <option value="">Select Product First</option>
        </select>
    </td>
    <td>
        <input type="text" name="stock_available_{{ $rowId }}" class="form-control stock-available" readonly value="0">
    </td>
    <td>
        <input type="number" name="items[{{ $rowId }}][quantity]" class="form-control quantity-input" min="1" required disabled value="1">
    </td>
    <td>
        <input type="number" name="items[{{ $rowId }}][unit_price]" class="form-control price-input" step="0.01" min="0" required value="0.00">
    </td>
    <td>
        <input type="text" name="items[{{ $rowId }}][sub_total]" class="form-control sub-total" readonly value="0.00">
    </td>
    <td>
        @if ($rowId > 0)
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
        @endif
    </td>
</tr>
