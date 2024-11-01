<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory,Notifiable;

    protected $fillable=[
        'company_image',
        'company_name',
        'instagram_link',
        'linked_in_link',
        'imageSSN',
        'livePhoto',
        'nationalId',
        'description',
        'phone_number_2',
        'mobile_number_1',
        'mobile_number_2',
        'fax_number',
        'whatsapp_number',
        'facebook_link',
        'tiktok_link',
        'website',
        'checkByAdmin',
        'status',
        'service_id',
        'user_id',
        'section_id'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function works()
    {
        return $this->hasMany(EmployeeWork::class,'employee_id','user_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class,'employee_id','user_id');
    }


    public function likes()
    {
        return $this->hasMany(Like::class , 'employee_id','user_id');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class , 'employee_id','user_id');
    }

}
