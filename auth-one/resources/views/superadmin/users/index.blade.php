@extends('master') 

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All System Users</h3>
                    <div class="card-tools">
                        <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Associated Entity</th>
            <th>Status</th>
            <th style="width: 150px">Actions</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sl = ($users->currentPage() - 1) * $users->perPage() + 1;
        @endphp
        @forelse ($users as $user)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($user->roles->isNotEmpty())
                        <span class="badge bg-primary">{{ ucfirst($user->roles->first()->name) }}</span>
                    @else
                        <span class="badge bg-secondary">User</span>
                    @endif
                </td>
                <td>
                    @if ($user->depo)
                        Depo: <strong>{{ $user->depo->name }}</strong>
                    @elseif ($user->distributor)
                        Distributor: <strong>{{ $user->distributor->name }}</strong> (Depo: {{ $user->distributor->depo->name ?? 'N/A' }})
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No users found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="card-footer clearfix">
    {{ $users->links('vendor.pagination.bootstrap-5') }}
</div>

            </div>
        </div>
    </section>
@endsection