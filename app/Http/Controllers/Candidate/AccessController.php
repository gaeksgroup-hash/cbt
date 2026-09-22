<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateUser;
use App\Services\CBT\ExamTokenResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function store(Request $request, ExamTokenResolver $resolver): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'string', 'max:40'],
            'token' => ['required', 'string', 'max:100'],
        ]);

        $id = strtoupper(trim($request->input('user_id')));
        $candidate = CandidateUser::where('public_id', $id)->first();
        $exam = $resolver->resolve($request->input('token'));
        $allowed = $candidate && ($candidate->status === 'active' ||
            ($candidate->status === 'pre_generated' && config('cbt.allow_pre_generated_candidates')));

        if (! $allowed || ! $exam || $exam->program?->slug !== 'sak') {
            return back()->withInput($request->only('user_id'))
                ->withErrors(['access' => 'User ID atau token tidak valid / tidak aktif. Periksa kembali data akses Anda.']);
        }

        $request->session()->regenerate();
        $request->session()->put('cbt.candidate_user_id', $candidate->id);
        $request->session()->put('cbt.exam_id', $exam->id);

        return redirect()->route('sak.exam.guide', ['exam' => $exam->slug], 303);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sak.landing');
    }
}
