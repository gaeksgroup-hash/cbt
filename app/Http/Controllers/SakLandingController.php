<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SakLandingController extends Controller
{
    /**
     * Display the SAK program foundation page.
     */
    public function __invoke(): View
    {
        return view('sak.landing');
    }
}
