<?php

namespace App\Models;

use App\Providers\RouteServiceProvider;
use App\Utils\UserTypeUtil;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use Notifiable;

    protected $guard = 'user';
    protected $fillable = [
        'name', 'email', 'password', 'fcm_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function isAuthorized($route_name)
    {
        $route_name = explode('/', $route_name)[0];
        return $this->is_super_user ||
                DB::table('user_role_permissions')
                    ->where('route_name', 'LIKE', $route_name)
                    ->join('user_roles', 'user_roles.id', '=', 'user_role_permissions.role_id')
                    ->where('user_roles.id', $this->role_id)
                    ->exists();
    }

    public function getImageUrl()
    {
        return $this->image ? URL::asset($this->image) : URL::asset('images/placeholders/user.png');
    }

    public function mainPageRoute()
    {
        return route('home');
    }
}
