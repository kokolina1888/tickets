<?php

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use App\OrderConfirmationNumberGenerator;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchaseTicketTest extends TestCase
{
	use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }


    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {
         $this->withoutExceptionHandling();

        OrderConfirmationNumber::shouldReceive('generate')->andReturn('ORDERCONFIRMATION1234');

         $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);

         $response =    $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertStatus(201);
// //!!!!!!!!!!!!!!!!!!!!!!!ticket_quantity

        $response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 9750,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ]            
            ]);


       $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }


    /** @test */
    function email_is_required_to_purchase_tickets()
    {

        $concert = factory(Concert::class)->states('published')->create();
        $response = $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);
        
        $response->assertStatus(422);

        $this->assertArrayHasKey('email', $response->decodeResponseJson()['errors']);       
    }

    /** @test */
    function quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);
        
        $this->assertValidationError('quantity', $response);       

    }

    /** @test */
    function quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        // $this->orderTickets($concert,
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);

        $this->assertValidationError('quantity', $response);
    }

    /** @test */
    function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'quantity' => 3,
            ]);
        $this->assertValidationError('payment_token', $response);
        
    }

    private function orderTickets($concert, $params)
    {
        $requestA = $this->app['request'];

       $response = $this->json('POST', "/concerts/{$concert->id}/orders", $params);

        $this->app['request'] = $requestA;

        return $response;

    }

    private function assertValidationError($field, $response)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($field, $response->decodeResponseJson()['errors']);
    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
       $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250])->addTickets(3);


       $this->orderTickets($concert, [
        'email' => 'john@example.com',
        'quantity' => 3,
        'payment_token' => 'invalid-payment-token',
        ])->assertStatus(422);

       $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ticketsRemaining());
   }

   /** @test */
   function cannot_purchase_tickets_to_an_unpublished_concert()
   {
        // $this->withoutExceptionHandling();
     $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(3);
     $this->orderTickets($concert, [
        'email' => 'john@example.com',
        'quantity' => 3,
        'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertStatus(404);
     $this->assertFalse($concert->hasOrderFor('john@example.com'));
     $this->assertEquals(0, $this->paymentGateway->totalCharges());
 }

 /** @test */
 function cannot_purchase_more_tickets_than_remain()
 {
    $concert = factory(Concert::class)->states('published')->create()->addTickets(50);

    $response = $this->orderTickets($concert, [
        'email' => 'john@example.com',
        'quantity' => 51,
        'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        // dd($response);
    $response->assertStatus(422);
    $this->assertFalse($concert->hasOrderFor('john@example.com'));

    $this->assertEquals(0, $this->paymentGateway->totalCharges());
    $this->assertEquals(50, $concert->ticketsRemaining());
}
/** @test */
// function cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
// {
//     $this->withoutExceptionHandling();

//     $concert = factory(Concert::class)->states('published')->create([
//         'ticket_price' => 1200
//         ])->addTickets(3);

//     $this->paymentGateway->beforeFirstCharge(function ($paymentGateway) use ($concert) {
//         // $requestA = $this->app['request'];
//         $response = $this->orderTickets($concert, [
//             'email' => 'personB@example.com',
//             'quantity' => 1,
//             'payment_token' => $this->paymentGateway->getValidTestToken(),
//             ]);

//         // $this->app['request'] = $requestA;

//         $response->assertStatus(422);
//         $this->assertFalse($concert->hasOrderFor('personB@example.com'));
//         $this->assertEquals(0, $this->paymentGateway->totalCharges());

//     }); 

//     $this->orderTickets($concert, [
//         'email' => 'personA@example.com',
//         'quantity' => 3,
//         'payment_token' => $this->paymentGateway->getValidTestToken(),
//         ]);

//     // dd($concert->orders()->first()->toArray());

//     $this->assertEquals(3600, $this->paymentGateway->totalCharges());
//     $this->assertTrue($concert->hasOrderFor('personA@example.com'));
//     $this->assertEquals(3, $concert->ordersFor('personA@example.com')->first()->ticketQuantity());
// }



}
