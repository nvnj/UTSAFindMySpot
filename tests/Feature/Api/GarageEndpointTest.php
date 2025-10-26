<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class GarageEndpointTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
