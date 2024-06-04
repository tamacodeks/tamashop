<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class SendtamaorderTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $faker = Faker::create();
        $user = User::find(259);
//        $response = $this->get('send-tama');
//
//        Log::info('status => '.$response->getStatusCode());
//        $response->assertStatus(200);
//
        \Session::start();
        $response = $this->actingAs($user,'web')
            ->call('POST', 'send-tama/confirm/order', array(
            "_token" => csrf_token(),
            "product_id" => "53k7h6",
            "sender_first_name" => $faker->firstName,
            "sender_last_name" => $faker->lastName,
            "sender_mobile" => $faker->e164PhoneNumber,
            "sender_email" => $faker->email,
            "receiver_first_name" => $faker->firstName,
            "receiver_last_name" => $faker->lastName,
            "receiver_mobile" => $faker->e164PhoneNumber,
            "receiver_email" => $faker->email,
            "order_comment" => $faker->text,
        ));
        $this->assertEquals(200, $response->getStatusCode());
    }
}
