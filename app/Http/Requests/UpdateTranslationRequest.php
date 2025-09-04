<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'       =>   'required|integer|exists:translations,id',
            'context' => 'sometimes|required|string|max:255',
            'locale' => 'sometimes|required|string|max:255',
            'translations' => 'sometimes|required|array',
            'translations.*' => 'sometimes|required|string|max:255',
        ];
    }
}
