<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\DB::delete('delete from addresses');
        \Illuminate\Support\Facades\DB::delete('delete from contacts');
        \Illuminate\Support\Facades\DB::delete('delete from users');
    }
}
