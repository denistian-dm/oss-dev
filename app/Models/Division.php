<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Division extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = ['code', 'name'];

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
