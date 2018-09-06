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
