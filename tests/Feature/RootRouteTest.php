<?php

namespace Tests\Feature;

use Tests\TestCase;

class RootRouteTest extends TestCase
{
    public function test_root_route_returns_ok_and_displays_gaeks_cbt(): void
    {
        $response =$this->get('/');

        $response->assertStatus(200);$response->assertSee('GAEKS CBT');
    }
}
