<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->is($this->user);
    }

    public function rules()
    {
        return [];
    }
}
