<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\ResponseService;

class StoreUser extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'min:3'],
            'last_name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'min:3', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'avatar' => ['nullable', 'string', 'max:36'],
            'address' => ['required', 'string', 'max:255', 'min:3'],
            'phone_number' => ['required', 'string', 'max:20'],
            'is_marketing' => ['nullable','string'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new ResponseService();

        throw new HttpResponseException($response->error(
            422, 
            "Failed to validate data", 
            $validator->errors()
        ));
    }
}
