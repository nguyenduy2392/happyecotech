<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageLeakTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_does_not_leak_block_json(): void
    {
        $this->seed();

        $html = $this->get('/gioi-thieu')->assertOk()->getContent();

        $this->assertStringNotContainsString('block_type', $html);
        $this->assertStringNotContainsString('is_enabled', $html);
    }
}
