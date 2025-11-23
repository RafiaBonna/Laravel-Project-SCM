@extends('master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h3>Sales Invoice List</h3>
        <a href="{{ route('superadmin.sales.create') }}" class="btn btn-primary">+ New Invoice</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Depo</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                            <td>{{ $invoice->depo->name ?? 'N/A' }}</td>
                            <td>{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'Pending' => 'badge bg-warning text-dark',
                                        'Approved' => 'badge bg-success',
                                        'Canceled' => 'badge bg-danger',
                                    ][$invoice->status] ?? 'badge bg-secondary';
                                @endphp
                                <span class="{{ $statusClass }}">{{ $invoice->status }}</span>
                            </td>
                            <td>{{ $invoice->creator->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('superadmin.sales.show', $invoice->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No sales invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
