<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Mail\Subscribe;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class SubscribeController extends Controller
{

    public function index()
    {

        $details = [
            'title' => 'New Ticket Alert!',
            'body' => 'There is a new ticket added regarding your application, please check at System.'
        ];

        Mail::to('emailpenerima@gmail.com')->send(new \App\Mail\Subscribe($details));
    }
}
