<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isEmpleado(): bool
    {
        return $this->role === 'empleado';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeEmpleados($query)
    {
        return $query->where('role', 'empleado');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->name;
    }

    public function canPerformAction($action): bool
    {
        return $this->isAdmin() || in_array($action, ['view', 'edit_profile']);
    }
}