@extends('master') 

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Add Raw Material</h6>
            <a href="{{ route('superadmin.raw-materials.index') }}" class="btn btn-secondary btn-sm">
                Back to List
            </a>
        </div>
        <div class="card-body">
            
            {{-- ERROR / ALERT DISPLAY --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    Please correct the following errors:
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('superadmin.raw-materials.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_of_measure">Unit <span class="text-danger">*</span></label>
                            <select class="form-control @error('unit_of_measure') is-invalid @enderror" id="unit_of_measure" name="unit_of_measure" required>
                                <option value="">Select Unit</option>
                                {{-- Your units here --}}
                                @php
                                    $units = ['KG', 'Litre', 'Pcs', 'Meter'];
                                @endphp
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}" {{ old('unit_of_measure') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                            @error('unit_of_measure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection