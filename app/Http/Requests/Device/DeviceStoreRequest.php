<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class DeviceStoreRequest extends FormRequest
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
            'pin' => 'required|integer',
            'type' => 'required|integer|in:1,2,3|exists:auth.devices,type_id',
            'uuid' => 'required|integer'
            // 'uuid' => 'required|integer|unique:auth.devices,uuid'
        ];
    }
}
