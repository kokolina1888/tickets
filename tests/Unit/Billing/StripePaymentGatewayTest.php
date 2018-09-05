<?php
namespace Tests\Unit\Billing;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StripePaymentGatewayTest extends TestCase
{
    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new StripePaymentGateway
        $paymentGateway = new StripePaymentGateway;

        // $token = \Stripe\Token::create([
        //     "card" => [
        //         "number" => "4242424242424242",
        //         "exp_month" => 1,
        //         "exp_year" => date('Y') + 1,
        //         "cvc" => "123"
        //     ]
        // ], ['api_key' => config('services.stripe.secret')])->id;
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $token = \Stripe\Token::create([
          "card" => [
            "number" => "4242424242424242",
           "exp_month" => 1,
           "exp_year" => date('Y') + 1,
            "cvc" => "314"
          ]
        ], ['api_key' => config('services.stripe.secret')])->id;

       

        // Create a new charge for some amount using a valid token
        $paymentGateway->charge(2500, $token);

        // Verify that the charge was completed successfully
    }
}