<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Responses\Response;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AddChildRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // throw a validationException with the translated error messages
        throw new ValidationException($validator, Response::Validation([], $validator->errors()));
    }
}
