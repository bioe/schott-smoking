<?php

namespace App\Http\Requests;

use App\Models\Station;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StationUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        //$this->station = id //Auto from laravel
        $rules = [];
        return  array_merge($rules, [
            'code' => ['alpha_dash', 'max:255', Rule::unique(Station::class)->ignore($this->station)],
            'name' => ['string', 'max:255'],
            'max_pax' => ['integer'],
            'stay_duration_seconds' => ['integer'],
            'warning_below_seconds' => ['integer'],
            'disable_next_entry_seconds' => ['integer'],
            'door_open_seconds' => ['integer'],
            'active' => ['boolean'],
        ]);
    }
}
