{{-- resources/views/superadmin/products/create.blade.php (Final Cleaned Form) --}}

@extends('master')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Form Wrapper - col-md-8 for centering --}}
        <div class="col-md-8 offset-md-2">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">➕ Add New Product</h3>
                    <div class="card-tools">
                        <a href="{{ route('superadmin.products.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('superadmin.products.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        
                        {{-- 🟢 Success Alert --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> **Success!** {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- 🔴 Global Validation Error Alert --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> **Data Save Failed!** Please check the input fields marked in red.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <h5 class="text-success mb-3">Product Details & Pricing</h5>
                        
                        {{-- Product Name --}}
                        <div class="form-group">
                            <label for="name">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name') 
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                            @enderror
                        </div>
                        
                        <div class="row">
                            {{-- Product Code / SKU --}}
                            <div class="col-md-6 form-group">
                                <label for="sku">Product Code / SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku') }}">
                                @error('sku') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>

                            {{-- Unit --}}
                            <div class="col-md-6 form-group">
                                <label for="unit">Unit (e.g., Pcs, Box, Kg) <span class="text-danger">*</span></label>
                                <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', 'pcs') }}" required>
                                @error('unit') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>
                        </div>

                        {{-- Financial Rates (Row 1: MRP, Retail, Distributor) --}}
                        <div class="row">
                            {{-- MRP --}}
                            <div class="col-md-4 form-group">
                                <label for="mrp">MRP <span class="text-danger">*</span></label>
                                <input type="number" name="mrp" id="mrp" class="form-control @error('mrp') is-invalid @enderror" 
                                       value="{{ old('mrp', 0.00) }}" step="any" min="0" required>
                                @error('mrp') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>
                            
                            {{-- Retail Rate --}}
                            <div class="col-md-4 form-group">
                                <label for="retail_rate">Retail Rate</label>
                                <input type="number" name="retail_rate" id="retail_rate" class="form-control @error('retail_rate') is-invalid @enderror" 
                                       value="{{ old('retail_rate', 0.00) }}" step="any" min="0">
                                @error('retail_rate') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>

                            {{-- 🎯 Distributor Rate (New Field) --}}
                            <div class="col-md-4 form-group">
                                <label for="distributor_rate">Distributor Rate</label>
                                <input type="number" name="distributor_rate" id="distributor_rate" class="form-control @error('distributor_rate') is-invalid @enderror" 
                                       value="{{ old('distributor_rate', 0.00) }}" step="any" min="0">
                                @error('distributor_rate') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>
                        </div>

                        {{-- Financial Rates (Row 2: Depo Price) --}}
                        <div class="row">
                            {{-- Depo Selling Price --}}
                            <div class="col-md-4 form-group">
                                <label for="depo_selling_price">Depo Selling Price</label>
                                <input type="number" name="depo_selling_price" id="depo_selling_price" class="form-control @error('depo_selling_price') is-invalid @enderror" 
                                       value="{{ old('depo_selling_price', 0.00) }}" step="any" min="0">
                                @error('depo_selling_price') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                             @error('description') 
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Save New Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection