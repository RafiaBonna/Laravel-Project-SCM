@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Invoice: {{ $invoice->invoice_number }}</h6>
            <div>
                <a href="{{ route('superadmin.raw-material-purchases.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-5" id="invoice-body">
            
            {{-- INVOICE HEADER: Company/Title (Updated Name) --}}
            <div class="text-center mb-5">
                <h1 class="font-weight-bold text-dark mb-1">OPTICHAIN SOLUTIONS</h1>
                <p class="text-muted">RAW MATERIAL PURCHASE INVOICE</p>
                <hr style="border-top: 3px solid #343a40;"> {{-- Dark line for separation --}}
            </div>

            {{-- MASTER DATA: Supplier and Invoice Info --}}
            <div class="row mb-5">
                <div class="col-6">
                    <p class="font-weight-bold text-dark mb-2 border-bottom pb-1">Supplier Details:</p>
                    <p class="mb-1"><strong>Name:</strong> {{ $invoice->supplier->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Address:</strong> {{ $invoice->supplier->address ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $invoice->supplier->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="font-weight-bold text-dark mb-2 border-bottom pb-1">Invoice Info:</p>
                    <p class="mb-1"><strong>Invoice No:</strong> <span class="text-primary font-weight-bold">{{ $invoice->invoice_number }}</span></p>
                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F, Y') }}</p>
                    <p class="mb-1"><strong>Recorded By:</strong> {{ $invoice->user->name ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- PURCHASE ITEMS TABLE --}}
            <h6 class="font-weight-bold text-dark mb-3">Purchased Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Material</th>
                            <th>Unit</th>
                            <th>Batch No.</th>
                            <th class="text-right" style="width: 15%;">Quantity</th>
                            <th class="text-right" style="width: 15%;">Unit Price</th>
                            <th class="text-right" style="width: 15%;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sl = 1; @endphp
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $item->rawMaterial->name ?? 'N/A' }}</td>
                            <td>{{ $item->rawMaterial->unit_of_measure ?? 'N/A' }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY --}}
            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <table class="table table-sm table-borderless float-right">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold text-right pr-4">Sub Total:</td>
                                <td class="text-right">{{ number_format($invoice->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-right pr-4">Discount:</td>
                                <td class="text-right text-danger">- {{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            {{-- Grand Total Highlighted --}}
                            <tr class="bg-primary text-white font-weight-bold h5">
                                <td class="text-right pr-4 pt-2 pb-2">GRAND TOTAL:</td>
                                <td class="text-right pt-2 pb-2">BDT {{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- NOTES --}}
            @if($invoice->notes)
            <div class="mt-4">
                <p class="font-weight-bold mb-1">Notes:</p>
                <p class="border p-2 bg-light">{{ $invoice->notes }}</p>
            </div>
            @endif
            
            {{-- REMOVED: Signature and Stamp Area as requested --}}


        </div>
    </div>
</div>

{{-- Printing CSS (Optional: Print করার সময় অপ্রয়োজনীয় অংশ লুকিয়ে রাখে) --}}
@push('styles')
<style>
    @media print {
        /* Hide unnecessary elements during print */
        .main-sidebar, .main-header, .content-header, .btn, .card-header {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
        }
        .card-body {
            /* Remove card padding for full print area */
            padding: 0 !important; 
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        /* Ensure primary color prints correctly (dark grey/black) */
        .bg-primary {
            background-color: #000000 !important; /* Forces black on print */
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact; 
        }
    }
</style>
@endpush
@endsection