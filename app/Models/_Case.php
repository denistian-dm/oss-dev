<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class _Case extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "cases";

    protected $fillable = [
        'reference',
        'member_id',
        'user_id',
        'category_id',
        'juklak_id',
        'case_status_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i'
    ];

    public function member()
    {
        return $this->belongsTo('App\Models\Member');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function juklak()
    {
        return $this->belongsTo('App\Models\Juklak');
    }

    public function case_status()
    {
        return $this->belongsTo('App\Models\CaseStatus');
    }

    public function case_detail()
    {
        return $this->hasMany('App\Models\CaseDetail');
    }
}
