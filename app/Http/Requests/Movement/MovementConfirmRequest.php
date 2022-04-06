<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class MovementConfirmRequest extends FormRequest
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
            'dtOrigin' => 'required',
            'nextState' => 'required|integer',
            'originalPositionId' => 'required|integer|exists:core.positions,id',
            'ruleId' => 'required|integer|exists:core.rules,id',
            'vehicleId' => 'required|integer|exists:core.vehicles,id',
        ];
    }
}
