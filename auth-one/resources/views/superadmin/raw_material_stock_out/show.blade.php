@extends('master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Raw Material Issue Slip: {{ $stockOut->slip_number }}</h6>
            <div>
                <a href="{{ route('superadmin.raw-material-stock-out.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print Slip
                </button>
            </div>
        </div>
        <div class="card-body p-5" id="issue-slip-body">
            
            {{-- SLIP HEADER: Company/Title --}}
            <div class="text-center mb-5">
                <h1 class="font-weight-bold text-dark mb-1">OPTICHAIN SOLUTIONS</h1>
                <p class="text-muted">RAW MATERIAL PRODUCTION/PROJECT ISSUE SLIP</p>
                <hr style="border-top: 3px solid #343a40;"> {{-- Dark line for separation --}}
            </div>

            {{-- MASTER DATA: Issue Info --}}
            <div class="row mb-5">
                <div class="col-6">
                    <p class="font-weight-bold text-dark mb-2 border-bottom pb-1">Issue Details:</p>
                    <p class="mb-1"><strong>Slip No:</strong> {{ $stockOut->issue_number ?? $stockOut->slip_number }}</p>
                    <p class="mb-1"><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($stockOut->issue_date)->format('d F, Y') }}</p>
                    <p class="mb-1"><strong>Issued By:</strong> {{ $stockOut->user->name ?? 'System User' }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="font-weight-bold text-dark mb-2 border-bottom pb-1">Destination:</p>
                    {{-- issued_to ক্ষেত্রটি factory_name দ্বারা প্রতিস্থাপন করা হলো --}}
                    <p class="mb-1"><strong>Issued To (Factory):</strong> {{ $stockOut->factory_name ?? 'N/A' }}</p> 
                    <p class="mb-1"><strong>Reference:</strong> {{ $stockOut->reference_number ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- ITEMS TABLE (Unit Cost এবং Total Cost যোগ করা হয়েছে) --}}
            <div class="row">
                <div class="col-12">
                    <p class="font-weight-bold text-dark mb-2 border-bottom pb-1">Issued Items:</p>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th class="text-center" style="width: 5%">#</th>
                                <th style="width: 35%">Material Name</th>
                                <th style="width: 15%">Batch No.</th>
                                <th class="text-right" style="width: 15%">Unit Cost</th> {{-- ✅ NEW COLUMN --}}
                                <th class="text-right" style="width: 15%">Issued Quantity</th>
                                <th style="width: 5%">Unit</th>
                                <th class="text-right" style="width: 10%">Total Cost</th> {{-- ✅ NEW COLUMN --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockOut->items as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->rawMaterial->name ?? 'N/A' }}</td>
                                <td>{{ $item->batch_number }}</td>
                                <td class="text-right">{{ number_format($item->unit_cost, 4) }}</td> {{-- ✅ NEW DATA --}}
                                <td class="text-right">{{ number_format($item->quantity_issued, 3) }}</td> {{-- quantity_issued ব্যবহার করা হলো --}}
                                <td>{{ $item->rawMaterial->unit_of_measure ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($item->total_cost, 2) }}</td> {{-- ✅ NEW DATA --}}
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- <tfoot> সেকশনটি সরিয়ে নিচে আলাদাভাবে টোটাল দেখানো হলো --}}
                    </table>
                </div>
            </div>

            {{-- TOTAL COST SUMMARY SECTION (স্ক্রিনশটের মতো করে যোগ করা হলো) --}}
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <table class="table table-sm table-borderless mt-3">
                        <tr class="font-weight-bold">
                            <td class="text-right border-top border-bottom pt-2 pb-2">Total Quantity Issued:</td>
                            <td class="text-right border-top border-bottom pt-2 pb-2">{{ number_format($stockOut->total_quantity_issued, 2) }}</td>
                        </tr>
                        <tr class="bg-primary text-white font-weight-bold">
                            <td class="text-right">GRAND TOTAL COST (৳):</td>
                            <td class="text-right">{{ number_format($stockOut->total_issue_cost, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- NOTES --}}
            @if($stockOut->notes)
            <div class="mt-4">
                <p class="font-weight-bold mb-1">Notes:</p>
                <p class="border p-2 bg-light">{{ $stockOut->notes }}</p>
            </div>
            @endif
            
            {{-- Signature Area --}}
            <div class="row mt-5 pt-5">
                <div class="col-4 text-center border-top pt-2">Issued By</div>
                <div class="col-4 text-center border-top pt-2">Received By (Production)</div>
                <div class="col-4 text-center border-top pt-2">Verified By</div>
            </div>


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
        .bg-dark, .bg-primary {
            background-color: #000000 !important; 
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            color-adjust: exact; 
        }
    }
</style>
@endpush
@endsection