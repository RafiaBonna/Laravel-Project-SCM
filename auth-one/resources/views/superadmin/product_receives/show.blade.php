{{-- resources/views/superadmin/product_receives/show.blade.php --}}

@extends('master') 

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">📝 Product Receive Invoice Details</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-light" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('superadmin.product-receives.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    {{-- Invoice Header Section --}}
                    <div class="row invoice-info mb-4">
                        <div class="col-sm-4 invoice-col">
                            {{-- ✅ ফিক্সড: null-এর জন্য অতিরিক্ত সুরক্ষা --}}
                            <b>Invoice No:</b> {{ $receive->receive_no ?? 'N/A' }}<br>
                            <b>Receive Date:</b> {{ isset($receive->receive_date) ? \Carbon\Carbon::parse($receive->receive_date)->format('d F, Y') : 'N/A' }}<br>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            {{-- ✅ ফিক্সড: receiver রিলেশনশিপ চেক করা হলো --}}
                            <b>Receiver:</b> {{ $receive->receiver->name ?? 'N/A' }}<br>
                            <b>Total Received Qty:</b> {{ number_format($receive->total_received_qty ?? 0, 2) }}
                        </div>
                        <div class="col-sm-4 invoice-col text-right">
                             <b>Note:</b> {{ $receive->note ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Invoice Items Table --}}
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Batch No</th>
                                        <th class="text-right">Received Qty</th>
                                        <th class="text-right">Cost Rate</th>
                                        <th class="text-right">Total Cost (Qty * Rate)</th>
                                        <th>Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // ✅ গ্র্যান্ড টোটাল ক্যালকুলেশন
                                        $totalAmount = 0;
                                    @endphp
                                    {{-- items যদি না থাকে, তবে একটি ফাঁকা array হিসেবে গণ্য করা হলো --}}
                                    @foreach($receive->items ?? [] as $index => $item)
                                        @php
                                            // Cost এবং Quantity কে নিরাপদে নিউমেরিক টাইপে cast করা হলো
                                            $costRate = (float)($item->cost_rate ?? 0);
                                            $receivedQty = (float)($item->received_quantity ?? 0);
                                            $itemTotal = $receivedQty * $costRate;
                                            $totalAmount += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            {{-- ✅ ফিক্সড: $item->product চেক করা --}}
                                            <td>{{ $item->product->name ?? 'Product Missing' }}</td>
                                            <td>{{ $item->batch_no ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($item->received_quantity ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($item->cost_rate ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($itemTotal, 2) }}</td> 
                                            <td>
                                                @if($item->expiry_date)
                                                    {{ \Carbon\Carbon::parse($item->expiry_date)->format('d-M-Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        {{-- Grand Total Cost ডিসপ্লে --}}
                                        <th colspan="5" class="text-right">Grand Total Cost:</th>
                                        <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Footer/Signature Section --}}
                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <p class="border-top pt-2">Receiver Signature</p>
                        </div>
                        <div class="col-6 text-center">
                            <p class="border-top pt-2">Prepared By: {{ $receive->receiver->name ?? 'System' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection