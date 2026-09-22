<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamToken extends Model {
    protected $table = 'exam_tokens';
    protected $fillable = ['exam_id', 'token_hash', 'token_hint', 'is_active', 'valid_from', 'valid_until'];
    protected $hidden = ['token_hash'];
    protected $casts = ['is_active' => 'boolean', 'valid_from' => 'datetime', 'valid_until' => 'datetime'];
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class, 'exam_id'); }
}
