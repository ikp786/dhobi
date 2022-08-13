<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function termsAndConditions()
    {
        $title  = 'Terms and Conditions';
        $data   = compact('title');
        return view('front.pages.terms-and-conditions', $data);
    }

    function faq()
    {
        $title  = 'faq';
        $data   = compact('title');
        return view('front.pages.faq', $data);
    }

}
