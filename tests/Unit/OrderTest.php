<?php

namespace Tests\Unit;

use App\Order;
use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrderTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
	function tickets_are_released_when_an_order_is_cancelled()
	{
		$concert = factory(Concert::class)->create()->addTickets(10);
		
		$order = $concert->orderTickets('jane@example.com', 5);
		$this->assertEquals(5, $concert->ticketsRemaining());
		$order->cancel();
		$this->assertEquals(10, $concert->ticketsRemaining());
		$this->assertNull(Order::find($order->id));
	}
}