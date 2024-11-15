<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone'];

    
    public function services()
    {
        return $this->hasMany(Service::class);
    }

   
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
