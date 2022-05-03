<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUser(User $user)
    {
        $this->withHeaders(['X-Phone-Number' => $user->phone_number]);

        return $this;
    }
}
