<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   
     public function authorize(): bool
   {
       return true;
   }

   public function rules()
   {
       return [
           'email' => 'required|email|exists:users,email',
           'password' => 'required|min:6'
       ];
   }
   public function messages()
   {
       return [
           'email.required' => 'Please enter the Email Address',
           'email.email' => 'Please enter a valid Email Address',
           'email.exists' => 'The Email Address is not registered ',
           'password.required' => 'Please enter the Password',
           'password.min' => 'Please enter atleast 6 characters in Password',
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
