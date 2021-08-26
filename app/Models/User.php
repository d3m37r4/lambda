<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

/**
 * @method static paginate(mixed $env)
 * @method static find(int $id)
 * @method static findOrFail(int $id)
 * @method static create(array $array)
 * @property int $Id
 * @property int id
 * @property string email
 * @property string name
 * @property string password
 */
class User extends Authenticatable {
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Sets a hash instead of a valid password.
     *
     * @param $password
     */
    public function setPasswordAttribute($password) {
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);
        }
    }
}
