<?php

namespace Tests\Feature;

use Tests\TestCase;

class SakRouteTest extends TestCase
{
    public function test_sak_route_returns_ok_and_displays_sertifikasi_ahli_kepabeanan(): void
    {
        $response =$this->get('/sak');

        $response->assertStatus(200);$response->assertSee('Sertifikasi Ahli Kepabeanan');
    }
}
