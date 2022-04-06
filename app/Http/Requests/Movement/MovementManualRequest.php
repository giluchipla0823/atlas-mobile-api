<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class MovementManualRequest extends FormRequest
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
            'nextState' => 'required|integer|exists:core.states,id',
            'positionId' => 'required|integer|exists:core.positions,id',
            'originalPositionId' => 'required|integer|exists:core.positions,id',
            'ruleId' => 'required|integer|exists:core.rules,id',
            'userId' => 'required|integer|exists:auth.users,id',
            'vehicleId' => 'required|integer|exists:core.vehicles,id',
        ];
    }
}
