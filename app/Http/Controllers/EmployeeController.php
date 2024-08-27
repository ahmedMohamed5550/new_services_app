<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;

class EmployeeController extends Controller
{
    use ApiResponseTrait;
    public function store(EmployeeRequest $request)
    {

        $validatedData = $request->validated();

        $image_ssn = $request->file('imageSSN')->store('employees_ssn' , 'uploads');
        $validatedData['imageSSN'] = $image_ssn;

        $live_photo = $request->file('livePhoto')->store('employees_live_photo' , 'uploads');
        $validatedData['livePhoto'] = $live_photo;

        $employee = Employee::create($validatedData);

        return $this->apiResponse('employee created successfully' , 200 , new EmployeeResource($employee));

    }
}
