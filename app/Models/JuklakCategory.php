<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JuklakCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];

    public function juklak()
    {
        return $this->hasMany('App\Models\Juklak');
    }

    public function cases()
    {
        return $this->hasManyThrough(_Case::class, Juklak::class);
    }
}
