<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // 아래 $timestamps 가 없으면 default 값으로 true, true 이면 database build 시 create_at, update_at 컬럼을 자동으로 찾아서 해당 컬럼이없으면 오류
    public $timestamps = false;

    protected $guard = 'bduser';
    protected $connection = 'mysql_ori';
    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'password_expired', 'name', 'create_ip', 'create_date', 'uptate_ip', 'update_date', 'password_changed', 'version'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    protected $primaryKey = 'id';
}
