<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model {
    protected $table = 'question_options';
    protected $fillable = ['question_id', 'option_key', 'option_text', 'is_correct', 'sort_order'];
    protected $hidden = ['is_correct'];
    protected $casts = ['is_correct' => 'boolean', 'sort_order' => 'integer'];
    public function question(): BelongsTo { return $this->belongsTo(Question::class, 'question_id'); }
}
