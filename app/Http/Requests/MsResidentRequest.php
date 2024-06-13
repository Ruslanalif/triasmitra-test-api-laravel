<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'BirthPlace' => 'nullable|string|max:100',
            'BirthDay' => 'nullable|date',
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
}
