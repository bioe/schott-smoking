<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [];
        $requiredOrNull = "required";
        if ($this->banner) {
            $requiredOrNull = "nullable";
        }
        return  array_merge($rules, [
            'title' => ['string', 'max:255'],
            'media' => [$requiredOrNull, 'file'],
            'active' => ['boolean']
        ]);
    }
}
