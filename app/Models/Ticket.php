<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'user_id',
        'project_id',
        'subject',
        'type',
        'description',
        'status',
        'response',
        'started_at',
        'ended_at'
    ];

    protected $appends = [
        'duration',
        'remainingTime'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function room()
    {
        return $this->hasOne(Room::class, 'ticket_id');
    }

    public function files()
    {
        return $this->hasMany(TicketFile::class);
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->started_at);
        $end = Carbon::parse($this->ended_at);
        if (!empty($start) && !empty($end)) {
            return Carbon::parse($end)->diffInDays(Carbon::parse($start));
        }
    }

    public function getRemainingTimeAttribute()
    {
        $now = Carbon::now();
        $end = Carbon::parse($this->ended_at);
        if (!empty($end)) {
            return Carbon::parse($now)->diffInDays(Carbon::parse($end));
        }
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public function getStartedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public function getEndedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}
