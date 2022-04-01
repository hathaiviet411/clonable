<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
        switch (Route::getCurrentRoute()->getActionMethod()) {
            case 'updateone':
                return $this->getCustomRuleUpdateOne();
            case 'changePassword':
                return $this->getCustomRuleChangePassword();
            case 'store':
                return $this->getCustomRuleStore();
            case 'update':
                return $this->getCustomRuleUpdate();
            default:
                return [];
        }
    }

    public function getCustomRuleUpdateOne()
    {
        return [
            "user_name" => "required|string",
            "user_code" => "required|unique:users,user_code,null,user_code,deleted_at,NULL",
            "roles*" => "required|string",
            "department_id" => "required|number",
            "password" => "required|string"
        ];
    }

    public function getCustomRuleChangePassword()
    {
        return [
            // 'password' => "required|min:8"
        ];
    }

    public function getCustomRuleStore()
    {
        return [
            "user_name" => "required|string",
            "user_code" => "required|unique:users,user_code,null,user_code,deleted_at,NULL",
            "password" => "required|string",
            "roles*" => "required",
            "department_id" => "nullable|integer"
        ];
    }

    public function getCustomRuleUpdate()
    {
        return [
            "roles*" => "required",
            "user_name" => "required|string|max:255",
            "password" => "nullable|min:8",
            "current_password" => "nullable|min:8",
            "department_id" => "nullable|integer"
        ];
    }

    public function messages()
    {
        return [
            'required' => trans('validation.required'),
            'date_format' => trans('validation.date_format'),
            'regex' => trans('validation.regex'),
            'numeric' => trans('validation.numeric'),
            'digits_between' => trans('validation.digits_between'),
            'exists' => trans('validation.exists')
        ];
    }
}
