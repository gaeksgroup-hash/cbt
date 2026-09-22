<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model {
    protected $table = 'admin_users';
    protected $fillable = ['name', 'email', 'password_hash', 'role', 'status', 'last_login_at'];
    protected $hidden = ['password_hash'];
    protected $casts = ['last_login_at' => 'datetime'];
}
