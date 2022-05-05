<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'unique:discounts,code',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'count' => [
                'required',
                'integer',
                'gt:0',
            ],
            'start_time' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'before:expiration_time',
                'after:now',
            ],
            'expiration_time' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after:start_time',
            ],
        ];
    }
}
