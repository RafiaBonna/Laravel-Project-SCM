{{-- resources/views/superadmin/product_receives/partials/receive_item_row.blade.php --}}

@php
    // $i হল Row Index
    // $products হল কন্ট্রোলার থেকে আসা প্রোডাক্টের তালিকা
    $itemData = old('items.' . $i, []);
@endphp

{{-- ✅ ফিক্স: শুধুমাত্র <tr> ট্যাগ দিয়ে শুরু করুন এবং বাইরে কোনো ট্যাগ রাখবেন না --}}
<tr id="row{{ $i }}" data-item-id="{{ $i }}">
    <td>
        {{-- Product Select --}}
        <select name="items[{{ $i }}][product_id]" 
                class="form-control product-select select2 @error('items.'.$i.'.product_id') is-invalid @enderror" 
                data-id="{{ $i }}" 
                required>
            <option value="">Select Product</option>
            @foreach($products as $id => $name)
                <option value="{{ $id }}" {{ (isset($itemData['product_id']) && $itemData['product_id'] == $id) ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('items.'.$i.'.product_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    <td>
        {{-- Batch No --}}
        <input type="text" name="items[{{ $i }}][batch_no]" class="form-control @error('items.'.$i.'.batch_no') is-invalid @enderror" 
               value="{{ $itemData['batch_no'] ?? '' }}" required placeholder="Batch No">
        @error('items.'.$i.'.batch_no') <span class="invalid-feedback">N/A</span> @enderror
    </td>
    
    {{-- Received Quantity --}}
    <td>
        <input type="number" name="items[{{ $i }}][received_quantity]" 
               class="form-control received-qty @error('items.'.$i.'.received_quantity') is-invalid @enderror" 
               value="{{ $itemData['received_quantity'] ?? '0.00' }}" min="0.01" step="any" required placeholder="Quantity">
        @error('items.'.$i.'.received_quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    
    {{-- Rate Fields (Readonly for reference) --}}
    <td class="text-right"><input type="number" class="form-control mrp-rate-{{ $i }} text-right" value="0.00" readonly></td>
    <td class="text-right"><input type="number" class="form-control retail-rate-{{ $i }} text-right" value="0.00" readonly></td>
    <td class="text-right"><input type="number" class="form-control distributor-rate-{{ $i }} text-right" value="0.00" readonly></td>
    <td class="text-right"><input type="number" class="form-control depo-selling-price-{{ $i }} text-right" value="0.00" readonly></td>

    {{-- ✅ Cost Rate Field (Required) --}}
    <td>
        <input type="number" name="items[{{ $i }}][cost_rate]" 
               class="form-control cost-rate @error('items.'.$i.'.cost_rate') is-invalid @enderror" 
               value="{{ $itemData['cost_rate'] ?? '0.00' }}" min="0.00" step="any" required placeholder="Cost Rate">
        @error('items.'.$i.'.cost_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>

    {{-- Production and Expiry Date --}}
    <td>
        <input type="date" name="items[{{ $i }}][production_date]" class="form-control" 
               value="{{ $itemData['production_date'] ?? '' }}">
    </td>
    <td>
        <input type="date" name="items[{{ $i }}][expiry_date]" class="form-control" 
               value="{{ $itemData['expiry_date'] ?? '' }}">
    </td>
    
    {{-- ✅ অ্যাকশন বাটন --}}
    <td>
        {{-- ডিলিট বাটন (যদি রো-এর ইন্ডেক্স 0 এর বেশি হয় তবেই ডিলিট করা যাবে) --}}
        @if($i > 0)
        <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove Item" data-row-id="row{{ $i }}">
            <i class="fas fa-times"></i>
        </button>
        @else
        <button type="button" class="btn btn-secondary btn-sm" disabled><i class="fas fa-times"></i></button>
        @endif
    </td>
</tr> 
{{-- ✅ ফিক্স: শুধুমাত্র </tr> ট্যাগ দিয়ে শেষ করুন --}}