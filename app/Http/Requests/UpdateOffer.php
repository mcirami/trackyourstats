<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOffer extends FormRequest
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
            'visibility' => 'required|numeric|min:0|max:2',
            'campaign' => 'numeric',
            'status' => 'required|numeric',
            'offer_type' => 'required|numeric|min:0|max:3',
            'description' => 'required',
            'url' => 'required',
            'payout' => 'required|numeric',
            'assigned' => 'array',
            'unassigned' => 'array',

            // Offer Cap
            'enable_cap' => 'bool',
            'cap_type' => 'numeric|min:0|max:1',
            'cap_interval' => 'numeric|min:0|max:3',
            'interval_cap' => 'numeric',

            // Bonus Offer
            'enable_bonus_offer' => 'bool',
            'required_sales' => 'numeric'
        ];
    }
}
