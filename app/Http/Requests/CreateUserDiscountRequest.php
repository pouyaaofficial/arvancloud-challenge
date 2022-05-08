<?php

namespace App\Http\Requests;

use App\Actions\CreateUser;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserDiscountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge(CreateUser::rules(), [
                'code' => [
                    'required',
                    'string',
                    'exists:discounts,code',
                ],
            ],
        );
    }
}
