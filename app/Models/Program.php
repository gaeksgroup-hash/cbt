<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model {
    protected $table = 'programs';
    protected $fillable = ['code', 'slug', 'name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function questionBanks(): HasMany { return $this->hasMany(QuestionBank::class, 'program_id'); }
    public function exams(): HasMany { return $this->hasMany(Exam::class, 'program_id'); }
}
