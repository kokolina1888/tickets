<?php
namespace App;

use App\TicketCodeGenerator;

class TicketCodeNumberGenerator implements TicketCodeGenerator
{
    public function generate($number)
    {
        
        
        return 'TICKETCODE'.$number;
    }
}