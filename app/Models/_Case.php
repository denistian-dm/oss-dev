<?php

namespace App\Models;

use Carbon\Carbon;
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

    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i'
    // ];

    // protected $appends = [
    //     'created_at'
    // ];
    
    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->setTimezone(geoip()->getLocation('111.68.29.30')->timezone)->format('Y-m-d H:i');
        return $date;
    }

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
        return $this->hasMany(CaseDetail::class, 'case_id');
    }

    public function bug_ticket()
    {
        return $this->belongsToMany(BugTicket::class, 'bug_ticket_case', 'case_id', 'bug_ticket_id');
    }
}
