@extends('master')

@section('content')

    {{-- Header Section: Title and Period Filter --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="mb-0 text-dark">Supply Chain Dashboard</h1>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="periodFilter" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-calendar-alt me-2"></i> Period: 22 Jul - 25 Aug
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="periodFilter">
                <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
            </ul>
        </div>
    </div>

    {{-- 1. Overview / Statistics Cards --}}
    <div class="row mb-4">
        {{-- Card 1: Current Inventory Stock --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Current Inventory Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3,500</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +3.1% vs last month</small>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Orders --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">10,982</div>
                            <small class="text-danger"><i class="fas fa-arrow-down"></i> -12.5% vs last month</small>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Revenue --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">IDR 5,491,000,000</div>
                            <small class="text-danger"><i class="fas fa-arrow-down"></i> -8.3% vs last month</small>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Suppliers --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">150</div>
                            <small class="text-muted">In Network</small>
                        </div>
                        <div class="col-auto"><i class="fas fa-truck-loading fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <div class="row">
        {{-- Left Column (8/12) --}}
        <div class="col-xl-8 col-lg-7">
            
            {{-- 2. Inventory Level Chart (Static Data) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Level</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i></a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"><a class="dropdown-item" href="#">Monthly</a></div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="text-muted mb-3">Stock Overview (Chart Rendered via JavaScript)</h6>
                    <div class="chart-bar">
                        {{-- Fixed-height wrapper div --}}
                        <div style="position: relative; height: 250px; width: 100%;">
                            <canvas id="inventoryLevelChart"></canvas>
                        </div>
                        <div class="d-flex justify-content-center mt-3 small">
                            {{-- ✅ FIXED: Label colors for gray/black chart --}}
                            <span class="me-4"><i class="fas fa-square" style="color: #6c757d;"></i>Current Stock</span>
                            <span><i class="fas fa-square" style="color: #343a40;"></i>Optimal Stock</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Shipment Tracking Map and List (Static Data) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Track Shipment</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            {{-- Map Container / Static Placeholder --}}
                            <div id="shipmentMap" style="height: 300px; width: 100%; border: 1px solid #ccc; background-color: #f5f5f5; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #aaa;">
                                <i class="fas fa-globe-americas fa-3x"></i>
                                <p class="ms-2 mt-2">Supply Chain Map Placeholder</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h6 class="mb-3">Recent Shipments</h6>
                            {{-- Static Shipment List --}}
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><div class="fw-bold">SHP-00281</div><small class="text-muted">Warehouse Bogor</small></div>
                                    <span class="badge bg-dark rounded-pill">Processing</span>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><div class="fw-bold">SHP-00282</div><small class="text-muted">Warehouse Tangerang</small></div>
                                    <span class="badge bg-gray rounded-pill">Delivered</span>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div><div class="fw-bold">SHP-00283</div><small class="text-muted">Warehouse Kukaring</small></div>
                                    <span class="badge bg-primary rounded-pill">In Transit</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       {{-- Right Column (4/12) --}}
        <div class="col-xl-4 col-lg-5">
            
            {{-- 3. Order Statistics (Black & White Heatmap) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Statistics</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i></a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"><a class="dropdown-item" href="#">Weekly</a></div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- STATIC HEATMAP GRID (Black and White/Grayscale) --}}
                    <div class="d-flex flex-column align-items-center">
                        <small class="text-muted mb-2">Order Frequency Heatmap (B&W)</small>
                        <div class="d-flex mb-1" style="font-size: 0.7rem;">
                            <span style="width: 25px;"></span>
                            <span style="width: 40px; text-align: center;">Mon</span>
                            <span style="width: 40px; text-align: center;">Tue</span>
                            <span style="width: 40px; text-align: center;">Wed</span>
                            <span style="width: 40px; text-align: center;">Thu</span>
                            <span style="width: 40px; text-align: center;">Fri</span>
                        </div>
                        
                        @php
                            // Heatmap data with intensity 1 (low) to 5 (high)
                            $heatmap_data = ['18 PM' => [3, 4, 2, 5, 3], '12 PM' => [5, 5, 4, 3, 2], '06 AM' => [1, 2, 3, 2, 1]];
                        @endphp

                        @foreach ($heatmap_data as $time => $intensities)
                            <div class="d-flex mb-1 align-items-center" style="font-size: 0.7rem;">
                                <span style="width: 25px; text-align: right; margin-right: 5px;">{{ $time }}</span>
                                @foreach ($intensities as $intensity)
                                    @php
                                        // Calculate opacity: 0.2 for intensity 1, up to 1.0 for intensity 5 (grayscale)
                                        $opacity = $intensity / 5; 
                                        // Use pure black (0, 0, 0) with calculated opacity for grayscale
                                        $color = 'rgba(0, 0, 0, ' . $opacity . ')';
                                    @endphp
                                    <div style="width: 40px; height: 20px; margin: 1px; background-color: {{ $color }}; border: 1px solid #e3e6f0;"></div>
                                @endforeach
                            </div>
                        @endforeach
                        <div class="mt-3 small text-muted">Light (Fewer) <i class="fas fa-arrow-right"></i> Dark (More Frequent)</div>
                    </div>
                </div>
            </div>
            
            {{-- 5. Alerts (Static Data) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Alerts</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-dark p-2 mb-2" role="alert">
                        <h6 class="alert-heading small fw-bold">Stockout Risk!</h6>
                        <p class="mb-0 small">Instant porridge - 2 days coverage left.</p>
                    </div>
                    <div class="alert alert-light p-2 mb-2" role="alert">
                        <h6 class="alert-heading small fw-bold">Expiry Date!</h6>
                        <p class="mb-0 small">Yogurt Cups - 45 units will expire in 3 days.</p>
                    </div>
                    <div class="alert alert-dark p-2 mb-2" role="alert">
                        <h6 class="alert-heading small fw-bold">Route Blockage!</h6>
                        <p class="mb-0 small">SHP-00283 delayed due to road obstruction.</p>
                    </div>
                    <div class="alert alert-light   p-2 mb-2" role="alert">
                        <h6 class="alert-heading small fw-bold">Capacity Warning!</h6>
                        <p class="mb-0 small">Bandung Cold Storage - 520/600 shelves occupied.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- JavaScript Section for Charts and Maps --}}
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('inventoryLevelChart').getContext('2d');
            
            if (ctx) {
                var inventoryLevelChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Current Stock',
                            // ✅ FIXED: Changed color to a medium gray
                            backgroundColor: '#6c757d', 
                            data: [4500, 4200, 5000, 4800, 5500, 5200, 5800],
                        }, {
                            label: 'Optimal Stock',
                            // ✅ FIXED: Changed color to a dark gray/black
                            backgroundColor: '#343a40', 
                            data: [4000, 4000, 4500, 4500, 5000, 5000, 5500],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, 
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, max: 8000, ticks: { padding: 10 } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endsection