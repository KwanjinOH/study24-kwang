<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Terms extends Model
{
    public $timestamps = false;

    protected $connection = 'mysql_ori';
    protected $table = 'terms';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'utiliztion', 'personal', 'location', 'marketing', 'update_date', 'version'
    ];
}
