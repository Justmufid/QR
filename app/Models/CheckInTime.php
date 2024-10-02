<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'check_in_time',
        'room',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
