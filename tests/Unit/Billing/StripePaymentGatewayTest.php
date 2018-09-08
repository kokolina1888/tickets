<?php
namespace Tests\Unit\Billing;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\StripePaymentGateway;
use App\Billing\PaymentFailedException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

   
    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

 
}