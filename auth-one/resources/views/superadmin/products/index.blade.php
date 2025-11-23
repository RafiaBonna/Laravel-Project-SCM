{{-- resources/views/superadmin/products/index.blade.php (Updated with Stock Alert) --}}

@extends('master') 

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📦 Product List / Entry</h3>
                    <div class="card-tools">
                        <a href="{{ route('superadmin.products.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table id="productTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Unit</th>
                                <th>MRP</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            @php
                                $stock_qty = $product->current_stock;
                                $stock_class = 'success'; // Default Green
                                
                                if ($stock_qty <= 10) {
                                    $stock_class = 'danger'; // Red Alert for <= 10
                                } elseif ($stock_qty <= 20) {
                                    $stock_class = 'warning'; // Yellow Alert for <= 20
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku ?? 'N/A' }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ number_format($product->mrp ?? 0, 2) }}</td>
                                
                                {{-- 🎯 Stock Alert Display --}}
                                <td>
                                    <span class="badge badge-{{ $stock_class }}" title="Low Stock Alert">
                                        {{ number_format($stock_qty, 2) }}
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="badge badge-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('superadmin.products.edit', $product->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- ... Other Actions ... --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
                </div>
            </div>
    </div>
</div>
@endsection