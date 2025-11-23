{{-- resources/views/superadmin/products/edit.blade.php (Updated with all fields) --}}

@extends('master')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Form Wrapper - col-md-8 for centering --}}
        <div class="col-md-8 offset-md-2">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">✍️ Edit Product: {{ $product->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('superadmin.products.index') }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('superadmin.products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        
                        <h5 class="text-warning mb-3">Product Details & Pricing</h5>
                        
                        {{-- Product Name --}}
                        <div class="form-group">
                            <label for="name">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="row">
                            {{-- Product Code / SKU --}}
                            <div class="col-md-6 form-group">
                                <label for="sku">Product Code / SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku', $product->sku) }}">
                                @error('sku') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            {{-- Unit --}}
                            <div class="col-md-6 form-group">
                                <label for="unit">Unit (e.g., Pcs, Box, Kg) <span class="text-danger">*</span></label>
                                <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', $product->unit) }}" required>
                                @error('unit') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>

                        {{-- Financial Rates (Row 1: MRP, Retail, Distributor) --}}
                        <div class="row">
                            {{-- MRP --}}
                            <div class="col-md-4 form-group">
                                <label for="mrp">MRP <span class="text-danger">*</span></label>
                                <input type="number" name="mrp" id="mrp" class="form-control @error('mrp') is-invalid @enderror" 
                                       value="{{ old('mrp', $product->mrp ?? 0.00) }}" step="any" min="0" required>
                                @error('mrp') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            
                            {{-- Retail Rate --}}
                            <div class="col-md-4 form-group">
                                <label for="retail_rate">Retail Rate</label>
                                <input type="number" name="retail_rate" id="retail_rate" class="form-control @error('retail_rate') is-invalid @enderror" 
                                       value="{{ old('retail_rate', $product->retail_rate ?? 0.00) }}" step="any" min="0">
                                @error('retail_rate') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            {{-- Distributor Rate --}}
                            <div class="col-md-4 form-group">
                                <label for="distributor_rate">Distributor Rate</label>
                                <input type="number" name="distributor_rate" id="distributor_rate" class="form-control @error('distributor_rate') is-invalid @enderror" 
                                       value="{{ old('distributor_rate', $product->distributor_rate ?? 0.00) }}" step="any" min="0">
                                @error('distributor_rate') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>

                        {{-- Financial Rates (Row 2: Depo Price) --}}
                        <div class="row">
                            {{-- Depo Selling Price --}}
                            <div class="col-md-4 form-group">
                                <label for="depo_selling_price">Depo Selling Price</label>
                                <input type="number" name="depo_selling_price" id="depo_selling_price" class="form-control @error('depo_selling_price') is-invalid @enderror" 
                                       value="{{ old('depo_selling_price', $product->depo_selling_price ?? 0.00) }}" step="any" min="0">
                                @error('depo_selling_price') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            
                            {{-- Status --}}
                            <div class="col-md-4 form-group">
                                <label for="is_active">Status</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                        
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-edit"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection