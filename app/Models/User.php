<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'gender',
        'age',
        'total_children',
        'total_daughters',
        'total_sons',
        'total_transgender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'total_children' => 'integer',
            'total_daughters' => 'integer',
            'total_sons' => 'integer',
            'total_transgender' => 'integer',
        ];
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }
}
