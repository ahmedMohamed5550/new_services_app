<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWork extends Model
{
    use HasFactory;

    protected $table= "employee_works";

    protected $fillable = [
        'employee_id',
        'image_url',
        'video_url',
    ];

    public function employee(){
        return $this->BelongsTo(User::class,'employee_id','id');
    }
}
