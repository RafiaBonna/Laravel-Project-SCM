{{-- This view displays a list of Pending Sales Invoices for Depo users.
    Assumes $invoices variable is passed from the Controller. --}}
@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">অনুমোদনের জন্য অপেক্ষমাণ ইনভয়েসসমূহ</h1>
    <p class="mb-4 text-gray-600">শুধুমাত্র আপনার ডিপোর জন্য বরাদ্দকৃত ইনভয়েসগুলি দেখানো হচ্ছে।</p>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ইনভয়েস নং</th>
                    <th class="py-3 px-6 text-left">তারিখ</th>
                    <th class="py-3 px-6 text-center">মোট টাকা (৳)</th>
                    <th class="py-3 px-6 text-center">তৈরি করেছেন (Super Admin)</th>
                    <th class="py-3 px-6 text-center">স্ট্যাটাস</th>
                    <th class="py-3 px-6 text-center">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse ($invoices as $invoice)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ $invoice->invoice_no }}</td>
                        <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                        <td class="py-3 px-6 text-center font-semibold">{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="py-3 px-6 text-center">{{ $invoice->creator->name ?? 'N/A' }}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="relative inline-block px-3 py-1 font-semibold text-xs leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                <span class="relative text-yellow-800">{{ $invoice->status }}</span>
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            {{-- Link to the approval page --}}
                            <a href="{{ route('depo.invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">পর্যালোচনা ও অনুমোদন</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">অনুমোদনের জন্য কোনো ইনভয়েস নেই।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection