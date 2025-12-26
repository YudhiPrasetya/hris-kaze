<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class PermitFormRequest extends FormRequest implements FormRequestInterface{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorized(){
        return Request::user()->canAny(['permit.edit', 'permit.update', 'permit.store', 'permit.create']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [

        ];
    }
}
