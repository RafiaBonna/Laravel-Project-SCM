@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Raw Material Wastage List</h6>
            <a href="{{ route('superadmin.wastage.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Wastage Entry
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th>Date</th>
                            <th>Raw Material</th>
                            <th>Batch No.</th>
                            <th class="text-right">Wasted Quantity</th>
                            <th class="text-right">Total Cost (৳)</th>
                            <th>Reason</th>
                            <th>Recorded By</th>
                            <th style="width: 15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($wastages as $wastage)
                            <tr>
                                <td>{{ $wastage->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($wastage->wastage_date)->format('d-M-Y') }}</td>
                                <td>{{ $wastage->rawMaterial->name ?? 'N/A' }}</td>
                                <td>{{ $wastage->batch_number }}</td>
                                <td class="text-right">{{ number_format($wastage->quantity_wasted, 3) }} {{ $wastage->rawMaterial->unit_of_measure ?? '' }}</td>
                                <td class="text-right">{{ number_format($wastage->total_cost, 2) }}</td>
                                <td>{{ Str::limit($wastage->reason, 40) }}</td>
                                <td>{{ $wastage->user->name ?? 'System' }}</td>
                                <td class="d-flex justify-content-start">
                                    {{-- View Button --}}
                                    <!-- <a href="{{ route('superadmin.wastage.show', $wastage->id) }}" class="btn btn-info btn-sm me-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a> -->
                                    
                                    {{-- ❌ DELETE BUTTON FORM ❌ --}}
                                    <form action="{{ route('superadmin.wastage.destroy', $wastage->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Wastage record and reverse the stock? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No wastage records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $wastages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection