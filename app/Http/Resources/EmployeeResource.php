<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee_name,
            'employee_email' => $this->employee_email,
            'employee_designation' => $this->employee_designation,
            'employee_department' => $this->employee_department,
            'employee_date_of_joining' => $this->employee_date_of_joining,
            'manager' => new ManagerResource($this->manager),
        ];
    }
}
