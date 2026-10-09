<?php

namespace Tests\Feature;

use Tests\TestCase;

class BootTest extends TestCase
{
    public function test_health_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_required_php_extensions_are_loaded(): void
    {
        foreach (['imagick', 'gd', 'intl'] as $extension) {
            $this->assertTrue(extension_loaded($extension), "PHP extension [$extension] is required.");
        }
    }
}
