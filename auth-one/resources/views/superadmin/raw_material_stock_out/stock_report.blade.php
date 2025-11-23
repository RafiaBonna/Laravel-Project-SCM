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
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Stock Report data will be shown here.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection