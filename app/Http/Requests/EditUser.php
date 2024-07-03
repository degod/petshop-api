<?php

namespace App\Http\Requests;

use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EditUser extends FormRequest
{
    public function __construct(private JwtAuthService $jwtAuthService)
    {
    }

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
     * @return array<string|mixed>
     */
    public function rules(): array
    {
        $token = \Request::bearerToken() ?? null;
        $user = $token ? $this->jwtAuthService->authenticate($token) : null;

        $userId = $user ? $user->id : null;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
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
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new ResponseService();

        throw new HttpResponseException($response->error(
            422,
            'Failed to validate data',
            $validator->errors()
        ));
    }
}
