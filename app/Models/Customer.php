<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;
    
    protected $primaryKey = 'CustomerID';
    protected $table = 'customers';
    
    protected $fillable = [
        'Name', 
        'Email', 
        'Password',
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
    
    // Email field accessor for authentication  
    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID');
    }
}
