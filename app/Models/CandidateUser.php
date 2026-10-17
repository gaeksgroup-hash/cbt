<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateUser extends Model {
    protected $table = 'candidate_users';
    protected $fillable = ['public_id', 'name', 'email', 'status', 'activated_at'];
    protected $casts = ['activated_at' => 'datetime'];
    public function attempts(): HasMany { return $this->hasMany(Attempt::class, 'candidate_user_id'); }
    public function entitlements(): HasMany { return $this->hasMany(CandidateEntitlement::class, 'candidate_user_id'); }
    public function attemptResets(): HasMany { return $this->hasMany(AttemptReset::class, 'candidate_user_id'); }
}
