<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOffer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization gets handled in the controller's middleware for now...
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
            'offer_name' => 'required|min:3',
            'url' => 'required',
            'offer_type' => 'required',
            'payout' => 'required|numeric',
            'status' => 'required|numeric',
            'is_public' => 'required|numeric',
        ];
    }
}
