<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class MovementCorrectFirstLaneRequest extends FormRequest
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
            'originalPositionId' => 'required|integer|exists:core.positions,id',
            'positionId' => 'required|integer|exists:core.positions,id',
            'length' => 'required',
            'ruleName' => 'required',
        ];
    }
}
