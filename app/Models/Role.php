<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Acl;
use Spatie\Permission\Models\Permission;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    public $guard_name = 'api';     
}
