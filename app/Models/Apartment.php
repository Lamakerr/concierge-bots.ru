<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 
        'floor', 
        'house_id', 
        'status'
    ];

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    public function residents()
    {
        return $this->belongsToMany(Resident::class, 'apartment_resident_table');
    }
}