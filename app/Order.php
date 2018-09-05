<?php

namespace App;

use App\Concert;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $guarded = [];

	public function tickets()
	{
		return $this->hasMany('App\Ticket');
	}

	// public function cancel()
	// {
	// 	foreach ($this->tickets as $ticket) {
	// 		$ticket->release();
	// 	}
	// 	$this->delete();
	// }
	// public static function fromReservation($reservation)
 //    {
 //        $order = self::create([
 //            'email' => $reservation->email(),
 //            'amount' => $reservation->totalCost(),
 //        ]);
 //        $order->tickets()->saveMany($reservation->tickets());
 //        return $order;
 //    }

	public function ticketQuantity()
	{
		return $this->tickets()->count();
	}

	public function concert()
	{
		return $this->belongsTo(Concert::class);
	}

	public function toArray()
	{
		return [
		'email' => $this->email,
		'ticket_quantity' => $this->ticketQuantity(),
		'amount' => $this->amount,
		];
	}
	public static function forTickets($tickets, $email, $amount)
	{
		$order = self::create([
			'email' => $email,
			'amount' => $amount,
			]);
		foreach ($tickets as $ticket) {
			$order->tickets()->save($ticket);
		}
		return $order;
	}


}
