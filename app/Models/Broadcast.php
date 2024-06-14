<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Broadcast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'tipe',
        'subjek',
        'isi',
    ]; 

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function projects() {
        return $this->belongsTo(Project::class);
    }

    public function getCreatedAtAttribute($date) {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public function getUpdatedAtAttribute($date) {
        return Carbon::parse($date)->format('Y-m-d');
    }
}
