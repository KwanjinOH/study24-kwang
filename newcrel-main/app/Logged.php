<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logged extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql_ori';
    protected $table = 'logged';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date_created', 'remote_ip', 'version'
    ];
}
