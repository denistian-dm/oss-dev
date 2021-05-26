<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugTicketStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];

    public function bug_ticket()
    {
        return $this->hasMany('App\Models\BugTicket');
    }

    public function bug_ticket_detail()
    {
        return $this->hasMany('App\Models\BugTicketDetail');
    }
}
