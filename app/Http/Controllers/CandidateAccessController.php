<?php

namespace App\Http\Controllers;

use App\Services\CBT\CandidateAccessService;
use App\Services\CBT\CandidateAccessSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class CandidateAccessController extends Controller
{
    private const INVALID_MESSAGE =
        'User ID atau token tidak valid / tidak aktif.';

    public function store(
        Request $request,
        CandidateAccessService $service,
        CandidateAccessSession $session
    ): Response {
        $session->clear($request);

        $input = $request->input('user_id');

        $safeUserId = is_string($input)
            ? substr($input, 0, 64)
            : '';

        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => ['required', 'string', 'max:64'],
                'token' => ['required', 'string', 'max:128'],
            ]
        );

        if ($validator->fails()) {
            return $this->invalid($safeUserId);
        }

        try {
            $result = $service->resolve(
                $request->input('user_id'),
                $request->input('token')
            );
        } catch (RuntimeException $exception) {
            Log::error('CBT access configuration error', [
                'exception_type' => $exception::class,
            ]);

            return response(
                'Layanan CBT sedang tidak tersedia. Silakan coba kembali.',
                503
            );
        }

        if (! $result) {
            return $this->invalid($safeUserId);
        }

        $session->grant(
            $request,
            $result['candidate'],
            $result['exam']
        );

        return redirect()->route(
            'sak.exam.guide',
            ['exam' => $result['exam']->slug],
            303
        );
    }

    public function logout(
        Request $request,
        CandidateAccessSession $session
    ): RedirectResponse {
        $session->clear($request);

        $request->session()->regenerate();

        return redirect()->route('sak.landing', [], 303);
    }

    private function invalid(string $userId): RedirectResponse
    {
        return redirect()
            ->route('sak.landing')
            ->withErrors([
                'access' => self::INVALID_MESSAGE,
            ])
            ->withInput([
                'user_id' => $userId,
            ]);
    }
}
