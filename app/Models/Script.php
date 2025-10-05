<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    //
    protected $fillable = ['title','content','user_id','language'];
protected $casts = [
    'content' => 'array',
];
}
