{{-- This view is for Depo users to review and approve/cancel a Sales Invoice.
    Assumes $invoice is passed from the controller. --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">ইনভয়েস পর্যালোচনা এবং অনুমোদন</h1>

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

    <div class="bg-white shadow-xl rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start border-b pb-4 mb-4">
            <div>
                <h2 class="text-2xl font-semibold text-indigo-600">ইনভয়েস নং #{{ $invoice->invoice_no }}</h2>
                <p class="text-gray-600">তারিখ: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</p>
                <p class="text-gray-600">তৈরি করেছেন: **{{ $invoice->creator->name ?? 'N/A' }}**</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-700 block">মোট মূল্য</span>
                <span class="text-4xl font-bold text-gray-800">{{ number_format($invoice->total_amount, 2) }} ৳</span>
                @php
                    $statusClass = [
                        'Pending' => 'bg-yellow-200 text-yellow-800',
                        'Approved' => 'bg-green-200 text-green-800',
                        'Canceled' => 'bg-red-200 text-red-800',
                    ][$invoice->status] ?? 'bg-gray-200 text-gray-800';
                @endphp
                <span class="relative inline-block px-3 py-1 font-semibold text-xs leading-tight mt-2">
                    <span aria-hidden class="absolute inset-0 opacity-50 rounded-full {{ $statusClass }}"></span>
                    <span class="relative">{{ $invoice->status }}</span>
                </span>
            </div>
        </div>

        {{-- Invoice Details --}}
        <h3 class="text-xl font-semibold mb-3 text-gray-700">বরাদ্দকৃত পণ্য ও ব্যাচ</h3>
        <div class="overflow-x-auto mb-6 border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">পণ্য</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ব্যাচ নং</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">পরিমাণ</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ইউনিট মূল্য</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">সাব টোটাল</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($invoice->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->product->name ?? 'N/A Product' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->stock->batch_no ?? 'স্টক বরাদ্দ হয়নি' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ number_format($item->unit_price, 2) }} ৳
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-gray-800">
                                {{ number_format($item->sub_total, 2) }} ৳
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Approval/Cancellation Actions (Only for Pending Invoices) --}}
        @if ($invoice->status == 'Pending')
            <div class="mt-8 pt-4 border-t border-gray-200">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">অ্যাকশন নিন</h3>
                <div class="flex space-x-4">
                    {{-- Approve Form --}}
                    <form action="{{ route('depo.invoices.approve', $invoice->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                            ইনভয়েস অনুমোদন করুন
                        </button>
                    </form>

                    {{-- Cancel Button (Opens Form for Reason) --}}
                    <button type="button"
                            id="cancel-button"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                        ইনভয়েস বাতিল করুন
                    </button>
                </div>

                {{-- Cancellation Reason Form (Initially Hidden) --}}
                <div id="cancellation-form" class="mt-4 p-4 border border-red-300 rounded-lg bg-red-50 hidden">
                    <h4 class="font-semibold text-red-700 mb-2">বাতিলের কারণ</h4>
                    <form action="{{ route('depo.invoices.cancel', $invoice->id) }}" method="POST">
                        @csrf
                        <textarea name="cancellation_reason" rows="3" required
                                  class="block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                  placeholder="বাতিলের কারণ লিখুন। (কমপক্ষে 10 অক্ষর)"></textarea>
                        @error('cancellation_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <button type="submit"
                                class="mt-3 bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            বাতিল নিশ্চিত করুন
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- Approved/Canceled Status Details --}}
            <div class="mt-8 pt-4 border-t border-gray-200">
                <h3 class="text-xl font-semibold mb-2 text-gray-700">অনুমোদন/বাতিলের বিস্তারিত</h3>
                @if ($invoice->status == 'Approved')
                    <p class="text-green-600">ইনভয়েসটি **{{ $invoice->approvedBy->name ?? 'N/A' }}** দ্বারা অনুমোদিত হয়েছে।</p>
                    <p class="text-gray-600 text-sm">অনুমোদনের সময়: {{ \Carbon\Carbon::parse($invoice->approved_at)->format('d-M-Y H:i A') }}</p>
                @elseif ($invoice->status == 'Canceled')
                    <p class="text-red-600">ইনভয়েসটি **{{ $invoice->approvedBy->name ?? 'N/A' }}** দ্বারা বাতিল করা হয়েছে।</p>
                    <p class="text-gray-600 text-sm">বাতিলের কারণ: <span class="font-medium italic">{{ $invoice->cancellation_reason }}</span></p>
                    <p class="text-gray-600 text-sm">বাতিলের সময়: {{ \Carbon\Carbon::parse($invoice->approved_at)->format('d-M-Y H:i A') }}</p>
                @endif
            </div>
        @endif

    </div>

    <a href="{{ route('depo.invoices.pending') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-900 font-medium transition duration-300">
        ← অপেক্ষমাণ ইনভয়েস তালিকায় ফিরে যান
    </a>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cancelButton = document.getElementById('cancel-button');
        const cancellationForm = document.getElementById('cancellation-form');

        if (cancelButton && cancellationForm) {
            cancelButton.addEventListener('click', function() {
                cancellationForm.classList.toggle('hidden');
            });
        }
    });
</script>
@endsection