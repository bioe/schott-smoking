<?php

namespace App\Http\Requests;

use App\Models\CostCenter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CostCenterUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        //$this->costcenter = id //Auto from laravel
        $rules = [];
        return  array_merge($rules, [
            'code' => ['string', 'max:255', Rule::unique(CostCenter::class)->ignore($this->costcenter)],
            'name' => ['string', 'max:255'],
        ]);
    }
}
