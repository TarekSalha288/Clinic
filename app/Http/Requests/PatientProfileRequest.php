<?php

namespace App\Http\Requests;

use App\Http\Responses\Response;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PatientProfileRequest extends FormRequest
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
            'birth_date' => 'required',
            'gender' => 'required',
            'age' => 'required',
            'blood_type' => 'required',
            'chronic_diseases' => 'required',
            'medication_allergies' => 'required',
            'permanent_medications' => 'required',
            'previous_surgeries' => 'required',
            'previous_illnesses' => 'required',
            'medical_analysis' => 'required',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // throw a validationException with the translated error messages
        throw new ValidationException($validator, Response::Validation([], $validator->errors()));
    }
}
