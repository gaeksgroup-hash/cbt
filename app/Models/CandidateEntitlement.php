<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateEntitlement extends Model
{
    protected $fillable = [
        'candidate_user_id',
        'entitlement_key',
        'source',
        'granted_by_admin_id',
        'revoked_at',
    ];

    protected $casts = ['revoked_at' => 'datetime'];

    public function candidateUser(): BelongsTo
    {
        return $this->belongsTo(CandidateUser::class, 'candidate_user_id');
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'granted_by_admin_id');
    }
}
