@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Raw Material Purchase Invoices</h6>
            <a href="{{ route('superadmin.raw-material-purchases.create') }}" class="btn btn-primary btn-sm">
                + New Purchase (Stock In)
            </a>
        </div>
        <div class="card-body">
            
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Discount</th>
                            <th>Grand Total</th>
                            <th>Recorded By</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $index => $invoice)
                        <tr>
                            <td>{{ $invoices->firstItem() + $index }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                            <td>{{ $invoice->supplier->name ?? 'N/A' }}</td>
                            <td>{{ number_format($invoice->discount_amount, 2) }}</td>
                            <td class="font-weight-bold">BDT {{ number_format($invoice->grand_total, 2) }}</td>
                            <td>{{ $invoice->user->name ?? 'N/A' }}</td>
                            <td>
                                {{-- <a href="#" class="btn btn-info btn-sm">View</a> --}}
                               <a href="{{ route('superadmin.raw-material-purchases.show', $invoice->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">কোনো ক্রয়ের চালান পাওয়া যায়নি।</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection