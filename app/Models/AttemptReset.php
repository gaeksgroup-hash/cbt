<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptReset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'candidate_user_id',
        'exam_id',
        'admin_user_id',
        'reset_after_attempt_no',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'reset_after_attempt_no' => 'integer',
        'created_at' => 'datetime',
    ];

    public function candidateUser(): BelongsTo
    {
        return $this->belongsTo(CandidateUser::class, 'candidate_user_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
