<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;
    
    protected $primaryKey = 'StaffID';
    protected $table = 'staff';

    protected $fillable = [
        'UserName',
        'Password', 
        'StaffName',
    ];

    protected $hidden = [
        'Password', 
        'remember_token'
    ];
    
    // Password field accessor for authentication
    public function getAuthPassword()
    {
        return $this->Password;
    }
}
