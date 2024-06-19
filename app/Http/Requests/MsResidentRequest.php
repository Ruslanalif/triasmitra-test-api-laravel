<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class MsResidentRequest extends FormRequest
{
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false;
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'IDCardNumber' => 'required|digits:16',
            'Name' => 'required|string|max:100',
            'BirthDay' => 'nullable|date',
            'BirthPlace' => 'nullable|string|max:100',
            'Gender' => 'nullable|string|max:1',
            'Province' => 'nullable|string|max:2',
            'Regency' => 'nullable|string|max:4',
            'District' => 'nullable|string|max:7',
            'Village' => 'nullable|string|max:10',
            'Address' => 'nullable|string',
            'Religion' => 'nullable|string|max:45',
            'MaritalStatus' => 'nullable|string|max:45',
            'Employment' => 'nullable|string|max:100',
            'Citizenship' => 'nullable|string|max:100',
            'FileURL' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'FgActive' => 'nullable|string|max:1',
            'UserID' => 'nullable|string',
            
        ];
    }
    
    public function messages()
    {
        return [
            'IDCardNumber.required' => 'ID Card Number field is required.',
            'IDCardNumber.digits:16' => 'ID Card Number must be 16 digits!!.',
            'Name.required' => 'Name field is required.',
            'Name.max:100' => 'Name max length 100.',
            'Name.max:100' => 'Name max length 100.',
            'BirthDay.date' => 'Birth Day must valid date format (yyyy-mm-dd).',
            'BirthPlace.max:100' => 'BirthPlace max length 100.',
            'Gender.max:1' => 'Gender max length 1.',
            'Province.max:2' => 'Province max length 2.',
            'Regency.max:4' => 'Regency max length 4.',
            'District.max:7' => 'District max length 7.',
            'Village.max:10' => 'Village max length 10.',
            'Religion.max:45' => 'Religion max length 45.',
            'MaritalStatus.max:45' => 'MaritalStatus max length 45.',
            'Employment.max:100' => 'Employment max length 100.',
            'Citizenship.max:100' => 'Citizenship max length 100.',
            'FileURL.image' => 'The file must be an image.',
            'FileURL.mimes:jpeg,png,jpg,gif' => 'The image must be a file of type: jpeg, png, jpg, gif.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'xStatus' => '0',
            'xMessage' => $errors,
        ], 422);

        throw new HttpResponseException($response);
    }
}
