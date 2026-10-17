<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Program;
use App\Models\Question;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the main GAEKS CBT landing page.
     */
    public function __invoke(): View
    {
        $schemaReady = Schema::hasTable('programs')
            && Schema::hasTable('exams')
            && Schema::hasTable('questions');

        return view('landing', [
            'programCount' => $schemaReady ? Program::where('is_active', true)->count() : 0,
            'examCount' => $schemaReady ? Exam::where('is_active', true)->count() : 0,
            'questionCount' => $schemaReady ? Question::where('is_active', true)->count() : 0,
        ]);
    }
}
