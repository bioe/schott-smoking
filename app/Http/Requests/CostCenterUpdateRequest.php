<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostCenterUpdateRequest extends FormRequest
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
            'code' => ['string', 'max:255'],
        ]);
    }
}
