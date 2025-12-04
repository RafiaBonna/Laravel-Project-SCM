{{-- resources/views/superadmin/raw_material_stock_out/stock_report.blade.php --}}

@extends('master') {{-- আপনার মাস্টার লেআউট এখানে ব্যবহার করুন --}}

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📦 Raw Material Stock Report (Current Stock)</h4>
    </div>

    {{-- Stock Report Table Structure --}}
    <div class="card">
        <div class="card-body">
            
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Raw Material Name</th>
                        <th>Current Stock Qty</th>
                        <th>Avg. Unit Cost</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
               <tbody>
                    {{-- Controller থেকে আসা $stockReportData লুপ করা হচ্ছে --}}
                    @forelse ($stockReportData as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        {{-- Raw Material Model থেকে নাম আনা হচ্ছে --}}
                        <td>{{ $item->rawMaterial->name ?? 'N/A' }} ({{ $item->rawMaterial->unit_of_measure ?? '' }})</td>
                        
                        {{-- গণনা করা স্টক কোয়ান্টিটি --}}
                        <td>{{ number_format($item->current_stock_qty, 2) }}</td> 
                        
                        {{-- গণনা করা গড় একক খরচ --}}
                        <td>{{ number_format($item->avg_unit_cost, 4) }}</td>
                        
                        {{-- গণনা করা মোট মূল্য --}}
                        <td>{{ number_format($item->total_value, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            কোনো স্টক রিপোর্ট ডেটা পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection