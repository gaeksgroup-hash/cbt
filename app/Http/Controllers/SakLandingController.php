<?php

namespace App\Http\Controllers;

use App\Models\CandidateUser;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SakLandingController extends Controller
{
    /**
     * Display the SAK program foundation page.
     */
    public function __invoke(Request $request): View
    {
        $program = Schema::hasTable('programs') ? Program::where('slug', 'sak')->first() : null;
        $candidate = Schema::hasTable('candidate_users')
            ? CandidateUser::find($request->session()->get('cbt.candidate_user_id'))
            : null;

        return view('sak.landing', [
            'program' => $program,
            'examCount' => $program?->exams()->where('is_active', true)->count() ?? 0,
            'questionCount' => $program?->questionBanks()->withCount([
                'questions' => fn ($query) => $query->where('is_active', true),
            ])->get()->sum('questions_count') ?? 0,
            'candidate' => $candidate?->status === 'active' ? $candidate : null,
        ]);
    }
}
