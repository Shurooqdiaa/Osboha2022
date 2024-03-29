<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SocialMediaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_addSocialMedia()
   {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer 1|PKQmrLQvLZUmYZ8cX6WXU44YcpI4RknzYhJQJ483',
        ])->post('/socialMedia/add-social-media', ['facebook' => 'Test Social Media']);

        $response->assertStatus(200);
    }
}
