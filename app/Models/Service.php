<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = ['name' , 'desc' , 'image' , 'section_id'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
