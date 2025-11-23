@extends('master') 

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Raw Material Stock Out (Issue)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Stock Out List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            {{-- Success/Error Messages (Placeholder) --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">All Issued Material Slips</h3>
                    <div class="card-tools">
                        <a href="{{ route('superadmin.raw-material-stock-out.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New Stock Out
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Issue Slip No.</th>
                                <th>Issue Date</th>
                                <th>Issued To</th>
                                <th>Total Items</th>
                                <th>Issued By</th>
                                <th style="width: 150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- $stockOuts variable will be passed from Controller --}}
                            @forelse ($stockOuts as $stockOut)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $stockOut->slip_number }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($stockOut->issue_date)->format('d M, Y') }}</td>
                                    <td>{{ $stockOut->issued_to }}</td>
                                    <td>{{ $stockOut->items->count() }}</td>
                                    <td>{{ $stockOut->user->name ?? 'System' }}</td>
                                    <td>
                                        {{-- View/Show Route (Need to be implemented) --}}
                                        <a href="{{ route('superadmin.raw-material-stock-out.show', $stockOut->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                                        
                                        {{-- Delete Form (Use with caution and confirmation) --}}
                                        <form action="{{ route('superadmin.raw-material-stock-out.destroy', $stockOut->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this issue slip? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No raw material stock out records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{-- Pagination will be added later when controller passes data --}}
                    {{-- {{ $stockOuts->links('vendor.pagination.bootstrap-5') }} --}}
                </div>

            </div>
        </div>
    </section>
@endsection