<?php
// app/Http/Requests/StoreDiscountRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules()
    {
        $discountId = $this->route('discount')?->id;
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('discounts')->ignore($discountId)],
            'percentage' => ['nullable','numeric','min:0.01','max:100'],
            'fixed_amount' => ['nullable','numeric','min:0'],
            'usage_limit' => ['nullable','integer','min:1'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after:starts_at'],
            'is_active' => ['nullable','boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if (empty($this->percentage) && empty($this->fixed_amount)) {
                $v->errors()->add('percentage', 'Cần nhập phần trăm hoặc số tiền cố định.');
            }
        });
    }
}
