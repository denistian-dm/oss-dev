<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id_member',
        'name',
        'email',
        'phone1',
        'phone2',
        'client_id'
    ];

    protected $appends = [
        'suggestion'
    ];

    public function getSuggestionAttribute()
    {
        return $this->name .' ['. $this->id_member .']';
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function cases()
    {
        return $this->hasMany('App\Models\_Case');
    }
}
