<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detailes extends Model
{
    // public $timestamps = false;

    protected $guard = 'bduser';
    protected $connection = 'mysql_ori';
    protected $table = 'user_detailes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'nick_name', 'birth', 'concern_1', 'concern_2', 'concern_3', 'concern_4', 'concern_5', 'concern_5_txt'
    ];
}
