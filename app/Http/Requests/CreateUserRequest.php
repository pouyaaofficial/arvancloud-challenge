<?php

namespace App\Http\Requests;

use App\Actions\CreateUser;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return CreateUser::rules();
    }
}
