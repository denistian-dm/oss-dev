<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Level extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = ['name'];

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
}
