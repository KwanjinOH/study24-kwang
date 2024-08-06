<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interests extends Model
{
    protected $connection = 'mysql_ori';
    protected $table = 'user_interests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'pnu', 'unit', 'memo', 'created_at', 'updated_at'
    ];
}
