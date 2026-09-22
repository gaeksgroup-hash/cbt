<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model {
    protected $table = 'question_banks';
    protected $fillable = ['program_id', 'code', 'name', 'version', 'is_active'];
    protected $casts = ['version' => 'integer', 'is_active' => 'boolean'];
    public function program(): BelongsTo { return $this->belongsTo(Program::class, 'program_id'); }
    public function questions(): HasMany { return $this->hasMany(Question::class, 'question_bank_id'); }
}
