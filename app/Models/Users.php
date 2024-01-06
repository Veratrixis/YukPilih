<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class Users extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory;
    
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'username',
        'password',
        'role',
        'division_id',
    ];
    protected $hidden = [
        'password',
    ];
    // protected $casts = [
    //     'password' => 'hashed',
    // ];
    public function getJWTIdentifier() { 
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    public function read_user($id) {
        return self::find($id);
    }
}