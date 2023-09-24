<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnoucementUpdateRequest extends FormRequest
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
            'title' => ['string', 'max:255'],
            'content' => ['string'],
            'active' => ['boolean']
        ]);
    }
}
