<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;

class PositionReloadRequest extends FormRequest
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
            'vehicleId' => 'required|integer|exists:core.vehicles,id',
            'ruleId' => 'required|integer',
            'visited' => 'required',
            'originalPositionId' => 'required|integer|exists:core.positions,id',
        ];
    }
}
