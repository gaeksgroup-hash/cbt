<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model {
    protected $table = 'exams';
    protected $fillable = ['program_id', 'code', 'slug', 'title', 'description', 'exam_type', 'duration_seconds', 'pass_score', 'attempt_limit', 'randomize_questions', 'randomize_options', 'result_review_mode', 'is_active'];
    protected $casts = ['duration_seconds' => 'integer', 'pass_score' => 'decimal:2', 'attempt_limit' => 'integer', 'randomize_questions' => 'boolean', 'randomize_options' => 'boolean', 'is_active' => 'boolean'];
    public function program(): BelongsTo { return $this->belongsTo(Program::class, 'program_id'); }
    public function tokens(): HasMany { return $this->hasMany(ExamToken::class, 'exam_id'); }
    public function examQuestions(): HasMany { return $this->hasMany(ExamQuestion::class, 'exam_id'); }
    public function questions(): BelongsToMany {
        return $this->belongsToMany(Question::class, 'exam_questions', 'exam_id', 'question_id')
            ->withPivot(['id', 'section_code', 'sort_order', 'weight', 'required'])
            ->withTimestamps();
    }
    public function attempts(): HasMany { return $this->hasMany(Attempt::class, 'exam_id'); }
}
