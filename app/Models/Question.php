<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model {
    protected $table = 'questions';
    protected $fillable = ['question_bank_id', 'code', 'version', 'type', 'title', 'stem', 'points', 'explanation', 'legal_reference', 'answer_key_json', 'scoring_rules_json', 'metadata_json', 'is_active'];
    protected $hidden = ['answer_key_json', 'scoring_rules_json'];
    protected $casts = ['version' => 'integer', 'points' => 'decimal:2', 'answer_key_json' => 'array', 'scoring_rules_json' => 'array', 'metadata_json' => 'array', 'is_active' => 'boolean'];
    public function questionBank(): BelongsTo { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
    public function options(): HasMany { return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('sort_order'); }
    public function examQuestions(): HasMany { return $this->hasMany(ExamQuestion::class, 'question_id'); }
    public function exams(): BelongsToMany {
        return $this->belongsToMany(Exam::class, 'exam_questions', 'question_id', 'exam_id')
            ->withPivot(['id', 'section_code', 'sort_order', 'weight', 'required'])
            ->withTimestamps();
    }
}
