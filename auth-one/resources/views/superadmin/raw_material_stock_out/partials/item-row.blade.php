{{-- resources/views/superadmin/raw_material_stock_out/partials/item-row.blade.php --}}
@php
    // $index variable is passed from create.blade.php
    $index = $index ?? 0;
@endphp

<tr data-row-index="{{ $index }}">
    <td>
        {{-- Raw Material Selection --}}
        <select name="items[{{ $index }}][raw_material_id]" class="form-control material-select @error('items.'.$index.'.raw_material_id') is-invalid @enderror" required>
            <option value="">Select Material</option>
            @foreach($rawMaterials as $material)
                <option value="{{ $material->id }}" 
                        data-unit="{{ $material->unit_of_measure }}"
                        {{ old('items.'.$index.'.raw_material_id') == $material->id ? 'selected' : '' }}>
                    {{ $material->name }}
                </option>
            @endforeach
        </select>
        @error('items.'.$index.'.raw_material_id')<span class="text-danger small">{{ $message }}</span>@enderror
    </td>
    <td>
        {{-- Batch Selection --}}
        <select name="items[{{ $index }}][batch_number]" class="form-control batch-select @error('items.'.$index.'.batch_number') is-invalid @enderror" required disabled>
            <option value="">Select Material First</option>
        </select>
        <span class="available-qty text-muted small d-block" data-max="0">Max: 0</span>
        @error('items.'.$index.'.batch_number')<span class="text-danger small">{{ $message }}</span>@enderror

        {{-- Hidden field to store stock ID for destroy/reversal process --}}
        <input type="hidden" name="items[{{ $index }}][raw_material_stock_id]" class="raw-material-stock-id-input" value="{{ old('items.'.$index.'.raw_material_stock_id', '') }}">
    </td>
    <td class="unit-of-measure">
        {{-- Display Unit of Measure (If old value exists, it tries to show the unit) --}}
        {{ $rawMaterials->where('id', old('items.'.$index.'.raw_material_id'))->first()->unit_of_measure ?? 'N/A' }}
    </td>
    <td>
        {{-- Quantity Input --}}
        <input type="number" step="0.001" name="items[{{ $index }}][quantity]" class="form-control issue-qty @error('items.'.$index.'.quantity') is-invalid @enderror" value="{{ old('items.'.$index.'.quantity') }}" min="0.001" disabled required>
        @error('items.'.$index.'.quantity')<span class="text-danger small">{{ $message }}</span>@enderror
        
        {{-- Hidden field to store Average Purchase Price (Unit Cost) --}}
        <input type="hidden" name="items[{{ $index }}][unit_price]" class="unit-price-input" value="{{ old('items.'.$index.'.unit_price', '0') }}">
    </td>
    <td class="text-center">
        {{-- Remove Button --}}
        <button type="button" class="btn btn-danger btn-sm remove-item-row">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>