@extends('master')

@section('content')
<div class="container mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">➕ Record New Raw Material Wastage</h6>
            <a href="{{ route('superadmin.wastage.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('superadmin.wastage.store') }}" method="POST">
                @csrf

                {{-- Wastage Details --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Wastage Date <span class="text-danger">*</span></label>
                        <input type="date" name="wastage_date" class="form-control @error('wastage_date') is-invalid @enderror" 
                               value="{{ old('wastage_date', date('Y-m-d')) }}" required>
                        @error('wastage_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Raw Material <span class="text-danger">*</span></label>
                        <select name="raw_material_id" id="raw_material_id" class="form-control select2 @error('raw_material_id') is-invalid @enderror" style="width: 100%;" required>
                            <option value="">Select Material</option>
                            @foreach ($rawMaterials as $material)
                                <option value="{{ $material->id }}" data-unit="{{ $material->unit_of_measure }}" 
                                    {{ old('raw_material_id') == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }} ({{ $material->unit_of_measure }})
                                </option>
                            @endforeach
                        </select>
                        @error('raw_material_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                        <select name="raw_material_stock_id" id="raw_material_stock_id" class="form-control select2 @error('raw_material_stock_id') is-invalid @enderror" style="width: 100%;" required disabled>
                            <option value="">Select Material First</option>
                        </select>
                        @error('raw_material_stock_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="stock_info" class="form-text text-muted">Available: 0.00 | Unit Cost: 0.00</small>
                    </div>
                </div>

                {{-- Wastage Quantity & Reason --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Wasted Quantity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.001" name="quantity_wasted" id="quantity_wasted" class="form-control @error('quantity_wasted') is-invalid @enderror" 
                                   value="{{ old('quantity_wasted') }}" min="0.001" placeholder="Enter Quantity" required>
                            <span class="input-group-text" id="unit_of_measure_display">Unit</span>
                            @error('quantity_wasted') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <label class="form-label">Reason for Wastage <span class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" 
                               value="{{ old('reason') }}" placeholder="e.g. Expired Batch, Production Error" required>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Record Wastage</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- Select2 initialization script (assuming you use Select2) --}}
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const materialSelect = document.getElementById('raw_material_id');
        const batchSelect = document.getElementById('raw_material_stock_id');
        const stockInfo = document.getElementById('stock_info');
        const unitDisplay = document.getElementById('unit_of_measure_display');

        // URL uses the WastageController route to fetch batches
        const batchUrlTemplate = '{{ route("superadmin.api.wastage.batches", ["rawMaterialId" => "MATERIAL_ID_PLACEHOLDER"]) }}';

        // --- Core Batch Loading Function ---
        const loadBatches = (materialId, oldBatchId = null) => {
            batchSelect.disabled = true;
            batchSelect.innerHTML = '<option value="">Loading...</option>';
            stockInfo.textContent = 'Available: 0.00 | Unit Cost: 0.00';
            
            if (!materialId) {
                batchSelect.innerHTML = '<option value="">Select Material First</option>';
                batchSelect.disabled = true;
                return;
            }

            // Dynamically replace placeholder with actual ID
            const url = batchUrlTemplate.replace('MATERIAL_ID_PLACEHOLDER', materialId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Select Batch</option>';
                    if (data.length > 0) {
                        data.forEach(batch => {
                            const selected = (oldBatchId == batch.id) ? 'selected' : '';
                            options += `<option value="${batch.id}" data-qty="${batch.stock_quantity}" data-price="${batch.unit_cost}" ${selected}>${batch.text}</option>`;
                        });
                        batchSelect.innerHTML = options;
                        batchSelect.disabled = false;
                        
                        // If oldBatchId is present, trigger batch change event manually
                        if (oldBatchId) {
                            batchSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        batchSelect.innerHTML = '<option value="">No stock available for this material</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading batches:', error);
                    batchSelect.innerHTML = '<option value="">Error loading batches</option>';
                    batchSelect.disabled = false;
                });
        };
        
        // --- Event Listeners ---
        
        // 1. On Material Change: Load Batches and Update Unit
        materialSelect.addEventListener('change', function() {
            const materialId = this.value;
            loadBatches(materialId);

            // Update Unit of Measure Display
            const selectedOption = this.options[this.selectedIndex];
            const unit = selectedOption.dataset.unit || 'Unit';
            unitDisplay.textContent = unit;

        });

        // 2. On Batch Change: Update Stock Info & Max Qty
        batchSelect.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            const qtyInput = document.getElementById('quantity_wasted');
            
            if (selected && selected.value) {
                const availableQty = selected.dataset.qty;
                const unitPrice = selected.dataset.price;
                
                stockInfo.textContent = `Available: ${availableQty} | Unit Cost: ${unitPrice}`;
                qtyInput.max = availableQty; // Set max limit for client-side validation
                qtyInput.value = ''; // Clear previous value to ensure validation is re-checked
            } else {
                stockInfo.textContent = 'Available: 0.00 | Unit Cost: 0.00';
                qtyInput.max = '';
            }
        });

        // --- Initial Load (Handle Old Input) ---
        const oldMaterialId = "{{ old('raw_material_id') }}";
        const oldBatchId = "{{ old('raw_material_stock_id') }}";
        
        if (oldMaterialId) {
            loadBatches(oldMaterialId, oldBatchId);
        }

        // Initial unit update (if an old material is selected)
        if (materialSelect.value) {
             const selectedOption = materialSelect.options[materialSelect.selectedIndex];
             const unit = selectedOption.dataset.unit || 'Unit';
             unitDisplay.textContent = unit;
        }

    });
</script>
@endsection