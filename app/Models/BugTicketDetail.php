<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BugTicketDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['comment', 'user_id', 'bug_ticket_id', 'bug_ticket_status_id'];

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->setTimezone(geoip()->getLocation('111.68.29.30')->timezone)->format('d-M-Y H:i');
        return $date;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function bug_ticket()
    {
        return $this->belongsTo('App\Models\BugTicket');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\BugTicketStatus');
    }
}
