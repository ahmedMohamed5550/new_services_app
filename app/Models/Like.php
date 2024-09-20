<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = ['user_id' , 'employee_id'];

    public function employee(){
        return $this->belongsTo(User::class,'employee_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }
}
