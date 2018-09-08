<?php

namespace App;

use App\Concert;
use Illuminate\Database\Eloquent\Model;
use App\Facades\OrderConfirmationNumber;
use App\OrderConfirmationNumberGenerator;

class Order extends Model
{
	protected $guarded = [];

	public function tickets()
	{
		return $this->hasMany('App\Ticket');
	}

	public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
    }

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
		'confirmation_number' => $this->confirmation_number,
		'email' => $this->email,
		'ticket_quantity' => $this->ticketQuantity(),
		'amount' => $this->amount,
		];
	}
	public static function forTickets($tickets, $email, $charge)
	{
		$order = self::create([
			'confirmation_number' => OrderConfirmationNumber::generate(),
			'email' => $email,
			'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour(),
			]);

		// dd($order);
		
		foreach ($tickets as $ticket) {
			$order->tickets()->save($ticket);
		}
		return $order;
	}


}
