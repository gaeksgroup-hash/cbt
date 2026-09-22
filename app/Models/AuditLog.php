<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
    public $timestamps = false;
    protected $table = 'audit_logs';
    protected $fillable = ['actor_type', 'actor_id', 'action', 'entity_type', 'entity_id', 'before_json', 'after_json', 'ip_address', 'request_id', 'created_at'];
    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'created_at' => 'datetime',
    ];
}
