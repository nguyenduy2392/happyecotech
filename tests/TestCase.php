<?php

namespace Tests;

use App\Support\JsonValue;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['happyecotech.admin_password' => Str::random(32)]);
    }

    protected function assertSameJsonValue(mixed $expected, mixed $actual): void
    {
        $this->assertSame(JsonValue::orderedKeys($expected), JsonValue::orderedKeys($actual));
    }
}
