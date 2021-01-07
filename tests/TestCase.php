<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private function getUser()
    {
        return new User(Str::uuid(), (string) time(), time()."@".time().".com.br", "haha");

    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->be($this->getUser(), "api");
        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
}
