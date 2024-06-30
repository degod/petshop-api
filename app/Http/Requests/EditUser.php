<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Services\ResponseService;
use App\Services\JwtAuthService;

class EditUser extends FormRequest
{
    public function __construct(private JwtAuthService $jwtAuthService){}
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
        $token = \Request::bearerToken() ?? null;
        $user = $this->jwtAuthService->authenticate($token);

        $userId = $user->id;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'avatar' => 'nullable|string|max:255',
            'is_marketing' => 'nullable',
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
