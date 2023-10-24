<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        //$this->employee = id //Auto from laravel
        $rules = [];
        return  array_merge($rules, [
            'staff_no' => ['string', 'max:255'],
            'card_id' => ['string', 'max:255', Rule::unique(Employee::class)->ignore($this->employee)],
            'name' => ['string', 'max:255'],
            'cost_center_id' => ['nullable'],
            'active' => ['boolean'],
            'maintenance' => ['boolean'],
        ]);
    }
}
