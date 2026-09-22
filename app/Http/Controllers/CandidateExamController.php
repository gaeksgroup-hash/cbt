<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\View\View;

class CandidateExamController extends Controller
{
    public function guide(Exam $exam): View
    {
        return view('sak.exams.guide', [
            'exam' => $exam,
        ]);
    }

    public function demo(Exam $exam): View
    {
        return view('sak.exams.demo', [
            'exam' => $exam,
        ]);
    }
}
