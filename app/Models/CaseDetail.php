<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use carbon\Carbon;

class CaseDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'deskripsi',
        'case_id',
        'user_id',
        'case_status_id'
    ];

    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    // ];

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->setTimezone(geoip()->getLocation('111.68.29.30')->timezone)->format('Y-m-d H:i');
        return $date;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function cases()
    {
        return $this->belongsTo('App\Models\_Case');
    }

    public function case_status()
    {
        return $this->belongsTo('App\Models\CaseStatus');
    }

    public function attachment()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }
}
