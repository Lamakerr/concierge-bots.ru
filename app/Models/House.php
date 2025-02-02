<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'street', 
        'number', 
        'building', 
        'floors', 
        'entrances'
    ];

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function getFilamentName(): string
    {   
        if($this->building) {
            return "ул.{$this->street}, дом {$this->number} Корпус: {$this->building}";
        } else {
            return "ул. {$this->street}, дом {$this->number}";
        }
        
    }
}