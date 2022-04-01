<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => "required|mimetypes:application/zip",
            'item_id' => "required|integer",
            //'month' => 'required|date_format:"Y-m-d"'
        ];
    }
}
