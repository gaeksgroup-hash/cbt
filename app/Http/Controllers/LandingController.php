<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the main GAEKS CBT landing page.
     */
    public function __invoke(): View
    {
        return view('landing');
    }
}
