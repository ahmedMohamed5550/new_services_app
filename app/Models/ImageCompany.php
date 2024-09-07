<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCompany extends Model
{
    use HasFactory;

    protected $fillable = ['image_path' , 'employee_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
