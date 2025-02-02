<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_username', 
        'name', 
        'сhat_id',
        'phone_number', 
        'resident_role_id', 
        'intercom_notices_agreement', 
        'danger_notices_agreement', 
        'status'
    ];

    public function role()
    {
        return $this->belongsTo(ResidentRole::class, 'resident_role_id');
    }

    public function apartments()
    {
        return $this->belongsToMany(Apartment::class, 'apartment_resident_table');
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

   
}