<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // যেহেতু এটি Super Admin-এর জন্য, তাই রোল/অথরাইজেশন চেক এখানে করা যেতে পারে,
        // তবে আমরা ধরে নিচ্ছি Middleware এটি ইতিমধ্যেই করে দিয়েছে।
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // ইনভয়েসের মূল ডেটার ভ্যালিডেশন
            'invoice_no' => ['required', 'string', 'max:50', 'unique:sales_invoices,invoice_no'],
            'invoice_date' => ['required', 'date_format:Y-m-d'],
            'depo_id' => ['required', 'exists:depos,id'],
            
            // একাধিক ইনভয়েস আইটেমের জন্য ভ্যালিডেশন
            'items' => ['required', 'array', 'min:1'], // কমপক্ষে একটি আইটেম থাকতে হবে
            
            // অ্যারের ভেতরের প্রতিটি আইটেমের ভ্যালিডেশন
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'], // পরিমাণ কমপক্ষে ১ হতে হবে
            'items.*.unit_price' => ['required', 'numeric', 'min:0'], // দাম ঋণাত্মক হতে পারবে না
        ];
    }

    /**
     * Custom error messages for specific fields.
     */
    public function messages(): array
    {
        return [
            'items.min' => 'বিক্রয়ের জন্য কমপক্ষে একটি পণ্য যুক্ত করতে হবে।',
            'items.*.product_id.exists' => 'নির্বাচিত পণ্যটি ডাটাবেসে বিদ্যমান নেই।',
            'items.*.quantity.min' => 'পণ্যের পরিমাণ কমপক্ষে ১ হতে হবে।',
            'invoice_no.unique' => 'এই ইনভয়েস নম্বরটি ইতিমধ্যেই ব্যবহৃত হয়েছে।',
            'depo_id.exists' => 'নির্বাচিত ডিপোটি বৈধ নয়।',
        ];
    }
}