<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize()
    {
        return true; // chỉnh nếu cần middleware auth
    }

    public function rules()
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'status' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png,webp|max:4096';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096';
        }

        return $rules;
    }
}
