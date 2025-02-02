<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentRole extends Model
{
    use HasFactory;

    protected $fillable = ['role'];
    protected $table = 'resident_roles';

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }
}