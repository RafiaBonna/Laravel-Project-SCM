{{-- resources/views/superadmin/depo/index.blade.php --}}

@extends('master') 

@section('content')
    {{-- ... Header অংশ ... --}}
    <section class="content">
        <div class="container-fluid">
            {{-- ... Success/Error মেসেজ ... --}}

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Depo Managers</h3>
                    <div class="card-tools">
                        {{-- Depo তৈরি করার জন্য User Management-এর Create পেজে নিয়ে যান --}}
                        <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Depo User
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                   <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Depo Name</th>
            <th>Manager Name</th>
            <th>Location</th>
            <th>Status</th>
            <th style="width: 150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sl = ($depos->currentPage() - 1) * $depos->perPage() + 1;
        @endphp
        @forelse($depos as $user)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $user->depo->name ?? 'N/A' }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->depo->location ?? 'N/A' }}</td>
                <td><span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($user->status) }}</span></td>
                <td>
                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user and associated Depo?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No Depo users found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="card-footer clearfix">
    {{ $depos->links('vendor.pagination.bootstrap-5') }}
</div>

            </div>
        </div>
    </section>
@endsection