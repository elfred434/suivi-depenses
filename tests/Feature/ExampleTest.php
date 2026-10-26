<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_l_application_retourne_une_reponse_succes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
