<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_vote_id',
        'user_id', 
        'post_id', 
        'option'
    ];

    public function post(){
        return $this->belongsTo( Post::class, 'post_id' );
    }

    public function user(){
        return $this->belongsTo( User::class, 'user_id');
    }
}
