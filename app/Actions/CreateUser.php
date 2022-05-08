<?php

namespace App\Actions;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateUser
{
    public function create($input): User
    {
        Validator::make($input, static::rules())->validate();

        $phoneNumber = data_get($input, 'phone_number');
        $user = User::firstWhere('phone_number', $phoneNumber);

        if (is_null($user)) {
            $user = User::create(['phone_number' => $phoneNumber]);
            UserCreated::dispatch($user);
        }

        return $user;
    }

    public static function rules(): array
    {
        return [
          'phone_number' => [
              'required',
              'digits:11',
          ],
      ];
    }
}
