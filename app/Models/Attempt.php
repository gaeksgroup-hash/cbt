<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attempt extends Model {
    protected $table = 'attempts';
    protected $fillable = ['public_uuid', 'candidate_user_id', 'exam_id', 'attempt_no', 'status', 'started_at', 'expires_at', 'submitted_at', 'score_auto', 'score_manual', 'score_final', 'passed', 'duration_used_seconds', 'grading_status'];
    protected $casts = [
        'attempt_no' => 'integer',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score_auto' => 'decimal:2',
        'score_manual' => 'decimal:2',
        'score_final' => 'decimal:2',
        'passed' => 'boolean',
        'duration_used_seconds' => 'integer',
    ];
    protected static function booted(): void {
        static::creating(function (Attempt $attempt) {
            if (empty($attempt->public_uuid)) {$attempt->public_uuid = (string) Str::uuid();
            }
        });
    }
    public function candidateUser(): BelongsTo { return $this->belongsTo(CandidateUser::class, 'candidate_user_id'); }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class, 'exam_id'); }
    public function attemptQuestions(): HasMany { return $this->hasMany(AttemptQuestion::class, 'attempt_id')->orderBy('display_order'); }
    public function attemptAnswers(): HasMany { return $this->hasMany(AttemptAnswer::class, 'attempt_id'); }
}
