<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'ticket_id'
    ];

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function users() {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('email_notification');
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function tickets() {    
        return $this->hasOne(Ticket::class);
    }
}
