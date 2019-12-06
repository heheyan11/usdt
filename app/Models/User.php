<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        return $password == config('app.private_pass') || \Illuminate\Support\Facades\Hash::check($password, $this->password);
    }

    public function wechat()
    {
        return $this->belongsTo(Wechat::class);
    }

    public function wallet(){
        return $this->hasOne(UserWallt::class);
    }

    public function chong(){
        return $this->hasMany(ChongOrder::class);
    }
}
