<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    public function show($id)
    {
    	return view('concerts.show', ['concert' => Concert::find($id)]);
    }
}
