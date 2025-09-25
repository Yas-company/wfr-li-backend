<?php

namespace App\Http\Controllers;


class PaymentController extends Controller
{
    public function success()
    {
        return view('payments.success');
    }

    public function fail()
    {
        return view('payments.fail');
    }
}
