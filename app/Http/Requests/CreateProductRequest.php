<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
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
            'category_uuid' => 'required|string|exists:categories,uuid',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'metadata' => 'required|string|json', // Validate that metadata is a JSON string
        ];
    }

    /**
     * Validate the request instance.
     */
    protected function passedValidation()
    {
        $metadata = json_decode($this->input('metadata'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = new ResponseService();
            throw new HttpResponseException($response->error(
                422,
                'Invalid JSON format for metadata',
                ['metadata' => ['Invalid JSON format']]
            ));
        }

        $validator = validator($metadata, [
            'brand' => 'required|string|exists:brands,uuid',
            'image' => 'required|string|exists:files,uuid',
        ]);

        if ($validator->fails()) {
            $response = new ResponseService();
            throw new HttpResponseException($response->error(
                422,
                'Failed to validate metadata',
                $validator->errors()
            ));
        }

        // Replace metadata string with the decoded array for further processing
        $this->merge(['metadata' => $metadata]);
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
