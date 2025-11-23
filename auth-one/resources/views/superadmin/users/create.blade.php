@extends('master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Create New User</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="container-fluid">
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">User Details</h3></div>

    <form action="{{ route('superadmin.users.store') }}" method="POST">
        @csrf
        <div class="card-body">

            {{-- Basic Details --}}
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Role & Status --}}
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="role_id">Role</label>
                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id')==$role->id?'selected':'' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            {{-- Dynamic Fields --}}
            <div id="dynamic-fields">

                {{-- Depo --}}
                <div class="row depo-fields dynamic-field" style="display:none;">
                    <div class="col-12"><div class="alert alert-info">Fields for Depo Manager</div></div>
                    <div class="form-group col-md-12">
                        <label for="depo_location">Depo Location (Optional)</label>
                        <input type="text" name="depo_location" class="form-control" value="{{ old('depo_location') }}">
                    </div>
                </div>

                {{-- Distributor --}}
                <div class="row distributor-fields dynamic-field" style="display:none;">
                    <div class="col-12"><div class="alert alert-info">Fields for Distributor</div></div>
                    <div class="form-group col-md-6">
                        <label for="distributor_depo_id">Assign to Depo</label>
                        <select name="distributor_depo_id" class="form-control">
                            <option value="">Select Depo</option>
                            @foreach($depos as $depo)
                                <option value="{{ $depo->id }}" {{ old('distributor_depo_id')==$depo->id?'selected':'' }}>{{ $depo->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="distributor_location">Distributor Location (Optional)</label>
                        <input type="text" name="distributor_location" class="form-control" value="{{ old('distributor_location') }}">
                    </div>
                </div>

            </div>

            {{-- Status --}}
            <div class="form-group">
                <label>User Status</label><br>
                <input type="radio" name="status" value="active" {{ old('status','active')=='active'?'checked':'' }}> Active
                <input type="radio" name="status" value="inactive" {{ old('status')=='inactive'?'checked':'' }}> Inactive
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    function toggleRoleFields(){
        var slug = $('#role_id').find(':selected').data('slug');
        $('.dynamic-field').hide();
        if(slug==='depo') $('.depo-fields').show();
        if(slug==='distributor') $('.distributor-fields').show();
    }
    toggleRoleFields();
    $('#role_id').change(toggleRoleFields);
});
</script>
@endsection
