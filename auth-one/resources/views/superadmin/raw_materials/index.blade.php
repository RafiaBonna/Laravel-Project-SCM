@extends('master') 

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Raw Material List</h6>
            <a href="{{ route('superadmin.raw-materials.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Raw Material
            </a>
        </div>
        <div class="card-body">
            
            {{-- SUCCESS / ERROR ALERT DISPLAY --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Material Name</th>
                            <th>Unit (UOM)</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rawMaterials as $index => $material)
                        <tr>
                            <td>{{ $rawMaterials->firstItem() + $index }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->unit_of_measure }}</td>
                            <td>{{ Str::limit($material->description, 50) }}</td>
                            <td>{{ $material->created_at->format('d M, Y') }}</td>
                            <td>
                                <a href="{{ route('superadmin.raw-materials.edit', $material->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                <form action="{{ route('superadmin.raw-materials.destroy', $material->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this raw material?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No raw material found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $rawMaterials->links() }}
            </div>
        </div>
    </div>
</div>
@endsection