<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Juklak extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contoh',
        'jawaban',
        'follow_up',
        'indikator',
        'juklak_category_id'
    ];

    public function juklak_category()
    {
        return $this->belongsTo('App\Models\JuklakCategory');
    }

    public function cases()
    {
        return $this->hasMany('App\Models\_Case');
    }
}
