<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'country_code' => 'required',
            'phone_number' => 'required',
            'country' => 'required',
            'password' => 'required',
            'address' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Please enter the Email Address',
            'email.unique' => 'Email Address is already registered',
            'email.email' => 'Please enter a valid Email Address',
            'password.required' => 'Please enter the Password',
            'name.required' => 'Please enter the Full Name',
            'country_code.required' => 'Please enter the Country Code',
            'phone_number.required' => 'Please enter the Phone Number',
            'address.required' => 'Please enter the Address',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessage = implode(', ', $validator->errors()->all());

        throw new HttpResponseException(response()->json([
            'status'   => false,
            'action' => $errorMessage
        ]));
    }
}
