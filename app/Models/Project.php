<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'priority_id',
        'leader_id',
        'company_id',
        'name',
        'contract',
        'contract_started_at',
        'contract_ended_at',
        'service_level_agreement',
        'jumlah_hari',
        'logo',
        'status',
        'started_at',
        'ended_at'
    ];

    protected $appends = [
        'duration',
        'fileUrl'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_employees', 'project_id', 'employee_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class);
    }

    public function programmers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    public function broadcast()
    {
        return $this->belongsToMany(Broadcast::class);
    }

    /**
     * Calculate project duration
     * 
     * @param   datetime    $started_at
     * @param   datetime    $ended_at
     * 
     * @return  string  duration
     */
    public function getDurationAttribute()
    {
        if (!empty(Carbon::parse($this->started_at)) && !empty(Carbon::parse($this->ended_at))) {
            return Carbon::parse($this->ended_at)->diffInDays(Carbon::parse($this->started_at));
        }
    }

    // Custom Accesstor
    public function getFileUrlAttribute()
    {
        return Storage::url('uploads/' . $this->logo);
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
