<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserException extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'week_id',
        'reason',
        'type',
        'duration,',
        'status',
        'start_at',
        'leader_note',
        'advisor_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

}