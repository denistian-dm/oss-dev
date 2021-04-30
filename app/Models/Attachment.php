<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'ext',
        'lokasi',
        'attachmentable_id',
        'attachmentable_type'
    ];

    public function attachmentable()
    {
        return $this->morphTo();
    }

    public function getLokasiAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
