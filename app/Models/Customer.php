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
        'Phone',
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
    
    // Accessors for FirstName and LastName (extracted from Name)
    public function getFirstNameAttribute()
    {
        return explode(' ', $this->Name)[0] ?? $this->Name;
    }
    
    public function getLastNameAttribute()
    {
        $parts = explode(' ', $this->Name);
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return '';
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID');
    }
}
