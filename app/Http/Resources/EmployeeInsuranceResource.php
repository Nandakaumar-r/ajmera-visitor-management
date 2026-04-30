<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeInsuranceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee_name,
            
            'spouse' => [
                'name' => $this->spouse_name,
                'dob' => $this->spouse_dob?->format('Y-m-d'),
                'aadhar' => $this->spouse_aadhar,
                'gender' => $this->spouse_gender,
            ],
            
            'child1' => [
                'name' => $this->child1_name,
                'dob' => $this->child1_dob?->format('Y-m-d'),
                'aadhar' => $this->child1_aadhar,
                'gender' => $this->child1_gender,
            ],
            
            'child2' => [
                'name' => $this->child2_name,
                'dob' => $this->child2_dob?->format('Y-m-d'),
                'aadhar' => $this->child2_aadhar,
                'gender' => $this->child2_gender,
            ],
            
            'premium' => (float) $this->premium,
            'family_count' => $this->family_count,
            'status' => $this->status,
            'submitted_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
