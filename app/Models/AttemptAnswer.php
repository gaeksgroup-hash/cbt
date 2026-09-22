<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model {
    protected $table = 'attempt_answers';
    protected $fillable = ['attempt_id', 'attempt_question_id', 'answer_json', 'is_marked_review', 'saved_at', 'is_correct', 'awarded_points', 'reviewer_score', 'reviewer_note'];
    protected $casts = [
        'answer_json' => 'array',
        'is_marked_review' => 'boolean',
        'saved_at' => 'datetime',
        'is_correct' => 'boolean',
        'awarded_points' => 'decimal:2',
        'reviewer_score' => 'decimal:2',
    ];
    public function attempt(): BelongsTo { return $this->belongsTo(Attempt::class, 'attempt_id'); }
    public function attemptQuestion(): BelongsTo { return $this->belongsTo(AttemptQuestion::class, 'attempt_question_id'); }
}
