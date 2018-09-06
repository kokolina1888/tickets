<?php
namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function show($confirmationNumber)
    {
        $order = Order::findByConfirmationNumber($confirmationNumber);
        // dd($order);
        return view('orders.show', ['order' => $order]);
    }

    public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
    }
}