@extends('master') 

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="mb-0 text-dark">ডিস্ট্রিবিউটর ম্যানেজমেন্ট</h1>
        <a href="{{ route('depo.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> নতুন ডিস্ট্রিবিউটর যোগ করুন
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ডিপোর অধীনে থাকা ডিস্ট্রিবিউটর তালিকা</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="distributorTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>নাম</th>
                            <th>ইমেইল</th>
                            <th>ফোন</th>
                            <th>তৈরির তারিখ</th>
                            <th>অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- $distributors ভ্যারিয়েবলটি Controller থেকে আসছে --}}
                        @forelse ($distributors as $distributor)
                            <tr>
                                <td>{{ $distributor->name }}</td>
                                <td>{{ $distributor->email }}</td>
                                <td>{{ $distributor->phone }}</td>
                                <td>{{ $distributor->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('depo.users.edit', $distributor) }}" class="btn btn-sm btn-info text-white me-2">Edit</a>
                                    <form action="{{ route('depo.users.destroy', $distributor) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ডিস্ট্রিবিউটরকে মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">কোনো ডিস্ট্রিবিউটর পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination Links --}}
            <div class="mt-3">
                {{ $distributors->links() }}
            </div>
        </div>
    </div>
@endsection