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
		'amount' => $this->amount,
		'tickets' => $this->tickets->map(function ($ticket) {
                return ['code' => $ticket->code];
            })->all(),
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

		//dd($order);
		// $key = 1;
		$tickets->each->claimFor($order);
		// foreach ($tickets as $ticket) {
		// 	$ticket->claimFor($order, $key);
		// 	$key++;
		// }


		return $order;
	}


}
