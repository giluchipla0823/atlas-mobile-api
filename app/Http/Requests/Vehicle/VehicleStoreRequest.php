<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'vin' => 'required|max:255',
            'modelId' => 'required|integer|exists:core.designs,id',
            'colorId' => 'required|integer|exists:core.colors,id',
            'countryId' => 'required|integer|exists:core.countries,id',
            'positionId' => 'required|integer|exists:core.positions,id',
            'compoundId' => 'required|integer|exists:auth.compounds,id'
        ];
    }
}
