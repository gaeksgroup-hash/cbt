<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model {
    protected $table = 'exam_questions';
    protected $fillable = ['exam_id', 'question_id', 'section_code', 'sort_order', 'weight', 'required'];
    protected $casts = ['sort_order' => 'integer', 'weight' => 'decimal:2', 'required' => 'boolean'];
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class, 'exam_id'); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class, 'question_id'); }
}
