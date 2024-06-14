<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasApiTokens, HasRoles;

    protected $table = 'users';

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'fullname',
        'email',
        'alamat',
        'handphone',
        'password',
        'company_id',
        'position_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be store to logs
     * 
     * @var array
     */
    protected static  $logAttributes = [
        'fullname',
        'email',
        'handphone',
        'password'
    ];

    protected static $logAttributesToIgnore = ['remember_token'];
    protected static $ignoreChangedAttributes = ['remember_token'];        

    public function findForPassport($username) {
        return $this->where('username', $username)
            ->orWhere('email', $username)
            ->first();
    }    

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function position() {
        return $this->belongsTo(Position::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function projects() {
        return $this->hasMany(Project::class, 'company_id', 'company_id');
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function rooms() {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function leadProjects() {
        return $this->hasMany(Project::class, 'leader_id');
    }    

    /**
     * A user can be MEMBER of many chat rooms
     * 
     * @return object memberships
     */
    public function memberships() {
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id')
            ->withTimestamps()
            ->withPivot('email_notification');
    }

    /**
     * Check if user owns a certain roo
     * 
     * @param   Room    $room
     * 
     * @return  boolean
     */
    public function isOwner(Room $room) {
        return $this->id === $room->owner_id;
    }

    /**
     * Check if user is Member of a certain room
     * 
     * @param Room $room Room object model
     * 
     * @return boolean
     */
    public function isMemberOf($room_id)
    {
        return $this->memberships->contains('id', $room_id);
    }    
}
