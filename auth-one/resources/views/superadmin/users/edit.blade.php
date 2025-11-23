@extends('master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Edit User: {{ $user->name }}</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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

<div class="card card-info">
    <div class="card-header"><h3 class="card-title">User Details</h3></div>

    <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">

            {{-- Basic Details --}}
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Role & Status --}}
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="role_id">Role</label>
                    @php
                        $currentRoleId = $user->roles->isNotEmpty() ? $user->roles->first()->id : null;
                        $currentDepo = $user->depo;
                        $currentDistributor = $user->distributor;
                    @endphp
                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" 
                                {{ old('role_id', $currentRoleId)==$role->id?'selected':'' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="password">New Password (Leave blank to keep old password)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            {{-- Dynamic Fields --}}
            <div id="dynamic-fields">

                {{-- Depo --}}
                <div class="row depo-fields dynamic-field" style="display:none;">
                    <div class="col-12"><div class="alert alert-info">Fields for Depo Manager</div></div>
                    <div class="form-group col-md-12">
                        <label for="depo_location">Depo Location (Optional)</label>
                        <input type="text" name="depo_location" class="form-control" 
                            value="{{ old('depo_location', $currentDepo ? $currentDepo->location : '') }}">
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
                                <option value="{{ $depo->id }}" 
                                    {{ old('distributor_depo_id', $currentDistributor ? $currentDistributor->depo_id : '')==$depo->id?'selected':'' }}>
                                    {{ $depo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="distributor_location">Distributor Location (Optional)</label>
                        <input type="text" name="distributor_location" class="form-control" 
                            value="{{ old('distributor_location', $currentDistributor ? $currentDistributor->location : '') }}">
                    </div>
                </div>

            </div>

            {{-- Status --}}
            <div class="form-group">
                <label>User Status</label><br>
                <input type="radio" name="status" value="active" {{ old('status', $user->status)=='active'?'checked':'' }}> Active
                <input type="radio" name="status" value="inactive" {{ old('status', $user->status)=='inactive'?'checked':'' }}> Inactive
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info">Update User</button>
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
