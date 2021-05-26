<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BugTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['juklak_id', 'bug_ticket_status_id'];

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->setTimezone(geoip()->getLocation('111.68.29.30')->timezone)->format('d-M-Y H:i');
        return $date;
    }

    public function bug_ticket_status()
    {
        return $this->belongsTo('App\Models\BugTicketStatus');
    }

    public function juklak()
    {
        return $this->belongsTo('App\Models\Juklak');
    }

    public function cases()
    {
        return $this->belongsToMany(_Case::class, 'bug_ticket_case', 'bug_ticket_id', 'case_id');
    }
}
