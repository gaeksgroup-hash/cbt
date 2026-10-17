<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateUser;
use App\Services\CBT\EntitlementVerificationUnavailable;
use App\Services\CBT\ExamTokenResolver;
use App\Services\CBT\GdpOrderEntitlementVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function store(
        Request $request,
        ExamTokenResolver $resolver,
        GdpOrderEntitlementVerifier $entitlements
    ): RedirectResponse
    {
        if (config('cbt.preview_only')) {
            return redirect()->route('sak.landing', status: 303);
        }

        $request->validate([
            'order_id' => ['required', 'string', 'max:40'],
            'token' => ['required', 'string', 'max:100'],
        ]);

        $orderId = $entitlements->normalizeOrderId($request->input('order_id'));
        $exam = $resolver->resolve($request->input('token'));
        $entitlementKey = strtoupper(trim((string) $exam?->program?->entitlement_key));

        if (! $exam || $exam->program?->slug !== 'sak' || $entitlementKey === '') {
            return $this->invalidAccess($request);
        }

        $candidate = CandidateUser::query()
            ->where('public_id', $orderId)
            ->where('status', 'active')
            ->whereHas('entitlements', fn ($query) => $query
                ->where('entitlement_key', $entitlementKey)
                ->whereNull('revoked_at'))
            ->first();

        if (! $candidate) {
            try {
                if (! $entitlements->allows($orderId, $entitlementKey)) {
                    return $this->invalidAccess($request);
                }
            } catch (EntitlementVerificationUnavailable) {
                return back()->withInput($request->only('order_id'))
                    ->withErrors(['access' => 'Verifikasi pembelian GDP sedang tidak tersedia. Coba kembali beberapa menit lagi.']);
            }
        }

        $candidate ??= CandidateUser::firstOrNew(['public_id' => $orderId]);
        if ($candidate->exists && $candidate->status === 'suspended') {
            return $this->invalidAccess($request);
        }
        $candidate->forceFill([
            'status' => 'active',
            'activated_at' => $candidate->activated_at ?? now(),
        ])->save();

        $request->session()->regenerate();
        $request->session()->put('cbt.candidate_user_id', $candidate->id);
        $request->session()->put('cbt.exam_id', $exam->id);
        $request->session()->put('cbt.order_reference', $orderId);
        $request->session()->put('cbt.entitlement_key', $entitlementKey);
        $request->session()->put('cbt.entitlement_verified_at', now()->timestamp);

        return redirect()->route('sak.exam.guide', ['exam' => $exam->slug], 303);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sak.landing');
    }

    private function invalidAccess(Request $request): RedirectResponse
    {
        return back()->withInput($request->only('order_id'))
            ->withErrors(['access' => 'User ID / Order ID atau token buku tidak cocok dengan akses ujian ini.']);
    }
}
