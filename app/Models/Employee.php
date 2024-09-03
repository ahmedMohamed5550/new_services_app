<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory,Notifiable;

    protected $fillable=[
        'description',
        'phone_number_1',
        'phone_number_2',
        'mobile_number_1',
        'mobile_number_2',
        'fax_number',
        'whatsapp_number',
        'facebook_link',
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
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function works()
    {
        return $this->hasMany(EmployeeWork::class, 'user_id', 'user_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }



}
