<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'bitTitle',
        'street',
        'specialMarque',
        'zipCode',
        'lat',
        'long',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
