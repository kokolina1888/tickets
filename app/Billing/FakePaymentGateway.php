<?php
namespace App\Billing;

use App\Billing\PaymentFailedException;
class FakePaymentGateway implements PaymentGateway
{
    private $charges;
    public function __construct()
    {
        // dd($this->charges);
        $this->charges = collect();
    }
    public function getValidTestToken()
    {
        return "valid-token";
    }
    public function charge($amount, $token)
    {
        // dd( $this->charges);
        if ($token !== $this->getValidTestToken()) {

            throw new PaymentFailedException;
            
        }

        $this->charges[] = $amount;


    }
    public function totalCharges()
    {
        // dd($this->charges);
        return $this->charges->sum();
    }
}