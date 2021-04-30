<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name'
    ];

    public function cases()
    {
        return $this->hasMany('App\Models\_Case');
    }

    public function case_details()
    {
        return $this->hasMany('App\Models\CaseDetails');
    }
}
