<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttemptQuestion extends Model {
    protected $table = 'attempt_questions';
    protected $fillable = ['attempt_id', 'question_id', 'question_version', 'question_snapshot_json', 'display_order', 'option_order_json', 'section_code', 'max_points'];
    protected $hidden = ['question_snapshot_json'];
    protected $casts = [
        'question_version' => 'integer',
        'question_snapshot_json' => 'array',
        'display_order' => 'integer',
        'option_order_json' => 'array',
        'max_points' => 'decimal:2',
    ];
    public function attempt(): BelongsTo { return $this->belongsTo(Attempt::class, 'attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class, 'question_id'); }
    public function answer(): HasOne { return $this->hasOne(AttemptAnswer::class, 'attempt_question_id'); }
}
