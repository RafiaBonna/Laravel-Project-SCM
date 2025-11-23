@extends('master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create New Supplier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.suppliers.index') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Supplier Details</h3>
                </div>
                
                <form action="{{ route('superadmin.suppliers.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- Row 1: Name and Contact Person --}}
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Enter supplier name" value="{{ old('name') }}" required>
                                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" id="contact_person" placeholder="Enter contact person name" value="{{ old('contact_person') }}">
                                @error('contact_person')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        {{-- Row 2: Phone and Email --}}
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
                                @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email address" value="{{ old('email') }}">
                                @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Row 3: Address and Status --}}
                        <div class="row">
                            <div class="form-group col-md-9">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control" id="address" rows="2" placeholder="Enter full address">{{ old('address') }}</textarea>
                                @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" id="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Supplier</button>
                        <a href="{{ route('superadmin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection