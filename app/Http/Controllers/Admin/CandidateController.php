<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Attempt;
use App\Models\AttemptReset;
use App\Models\AuditLog;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Services\CBT\AttemptQuota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $candidates = CandidateUser::query()
            ->withCount('attempts')
            ->when($search !== '', fn ($query) => $query->where(function ($nested) use ($search) {
                $nested->where('public_id', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            }))
            ->latest('updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.candidates.index', [
            'candidates' => $candidates,
            'search' => $search,
            'candidateCount' => CandidateUser::count(),
            'activeCandidateCount' => CandidateUser::where('status', 'active')->count(),
            'attemptCount' => Attempt::count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $requestedId = strtoupper(trim((string) $request->input('public_id', '')));
        $validated = $request->validate([
            'public_id' => [
                'nullable',
                'string',
                'max:40',
                'regex:/^CBT-[A-Z0-9]{6,24}$/',
                Rule::unique('candidate_users', 'public_id'),
            ],
            'name' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:254'],
        ], [
            'public_id.regex' => 'User ID manual harus diawali CBT- dan berisi 6 sampai 24 huruf atau angka.',
        ]);

        $admin = $this->admin($request);
        $candidate = DB::transaction(function () use ($validated, $requestedId, $admin, $request) {
            $publicId = $requestedId !== '' ? $requestedId : 'CBT-'.strtoupper(Str::random(12));
            while (CandidateUser::where('public_id', $publicId)->exists()) {
                $publicId = 'CBT-'.strtoupper(Str::random(12));
            }

            $candidate = CandidateUser::create([
                'public_id' => $publicId,
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
                'status' => 'active',
                'activated_at' => now(),
            ]);
            $candidate->entitlements()->create([
                'entitlement_key' => 'SAK',
                'source' => 'admin',
                'granted_by_admin_id' => $admin->id,
            ]);
            $this->audit($request, $admin, 'candidate.created', $candidate, null, [
                'public_id' => $candidate->public_id,
                'entitlement_key' => 'SAK',
            ]);

            return $candidate;
        });

        return redirect()->route('admin.candidates.show', $candidate)
            ->with('success', 'User ID '.$candidate->public_id.' berhasil dibuat dengan akses SAK.');
    }

    public function show(Request $request, CandidateUser $candidate, AttemptQuota $quota): View
    {
        $candidate->load(['entitlements' => fn ($query) => $query->whereNull('revoked_at')]);
        $exams = Exam::query()->with('program')->where('is_active', true)->orderBy('code')->get();
        $attempts = $candidate->attempts()->with('exam')->latest('id')->get();
        $examUsage = $exams->mapWithKeys(fn (Exam $exam) => [$exam->id => $quota->usage($candidate, $exam)]);

        return view('admin.candidates.show', compact('candidate', 'exams', 'attempts', 'examUsage'));
    }

    public function resetAttempts(Request $request, CandidateUser $candidate, Exam $exam): RedirectResponse
    {
        $admin = $this->admin($request);
        $cutoff = (int) $candidate->attempts()->where('exam_id', $exam->id)->max('attempt_no');

        AttemptReset::create([
            'candidate_user_id' => $candidate->id,
            'exam_id' => $exam->id,
            'admin_user_id' => $admin->id,
            'reset_after_attempt_no' => $cutoff,
            'reason' => 'Reset melalui panel admin',
            'created_at' => now(),
        ]);
        $this->audit($request, $admin, 'candidate.attempt_quota_reset', $candidate, null, [
            'exam_id' => $exam->id,
            'exam_code' => $exam->code,
            'reset_after_attempt_no' => $cutoff,
        ]);

        return back()->with('success', 'Kuota '.$exam->code.' direset. User mendapat tiga attempt baru.');
    }

    public function updateStatus(Request $request, CandidateUser $candidate): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['active', 'suspended'])]]);
        $admin = $this->admin($request);
        $before = ['status' => $candidate->status];
        $candidate->update(['status' => $validated['status']]);
        $this->audit($request, $admin, 'candidate.status_updated', $candidate, $before, [
            'status' => $candidate->status,
        ]);

        return back()->with('success', $candidate->status === 'active'
            ? 'Akses User ID diaktifkan.'
            : 'Akses User ID ditangguhkan.');
    }

    private function admin(Request $request): AdminUser
    {
        return $request->attributes->get('cbt.admin');
    }

    /** @param array<string, mixed>|null $before @param array<string, mixed>|null $after */
    private function audit(
        Request $request,
        AdminUser $admin,
        string $action,
        CandidateUser $candidate,
        ?array $before,
        ?array $after
    ): void {
        AuditLog::create([
            'actor_type' => 'admin',
            'actor_id' => $admin->id,
            'action' => $action,
            'entity_type' => 'candidate_user',
            'entity_id' => $candidate->id,
            'before_json' => $before,
            'after_json' => $after,
            'ip_address' => $request->ip(),
            'request_id' => (string) Str::uuid(),
            'created_at' => now(),
        ]);
    }
}
