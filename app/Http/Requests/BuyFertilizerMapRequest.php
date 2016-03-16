<?php

namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;
use Gis\Models\SystemCode;

class BuyFertilizerMapRequest extends Request {
    /**
     *
     * @var SEARCH_MAX_LENGTH_USERNAME
     */
    const MAX_LENGTH_USERNAME = 200;

    /**
     *
     * @var MAX_LENGTH_EMAIL
     */
    const MAX_LENGTH_EMAIL = 100;
    /**
     *
     * @var MAX_LENGTH_PHONE_NUMBER
     */
    const MAX_LENGTH_PHONE_NUMBER = 20;
    /**
     *
     * @var MAX_LENGTH_ACCOUNT_NUMBER
     */
    const MAX_LENGTH_ACCOUNT_NUMBER = 50;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array (
            'username' => 'required|max:' . self::MAX_LENGTH_USERNAME,
            'email' => 'required|email|max:' . self::MAX_LENGTH_EMAIL,
            'phonenumber' => 'required|max:' . self::MAX_LENGTH_PHONE_NUMBER,
            'cardnumber' => 'required|max:' . self::MAX_LENGTH_ACCOUNT_NUMBER,
        );
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return array (
            'username.required' => trans ( 'common.user_registration_username_required' ),
            'username.alpha_num' => trans ( 'common.user_registration_username_alpha_num' ),
            'username.max' => trans ( 'common.user_registration_username_max' ),
            'email.required' => trans ( 'common.user_registration_email_required' ),
            'email.email' => trans ( 'common.user_registration_email_email' ),
            'email.max' => trans ( 'common.user_registration_email_max' ),
        );
    }

    /**
     * handler after validate request.
     *
     * @return array
     */
    public function response(array $validateErrors) {
        $messages = array ();
        if (! empty ( $validateErrors )) {
            foreach ( $validateErrors as $fieldErrors ) {
                foreach ( $fieldErrors as $errorMessage ) {
                    array_push ( $messages, $errorMessage );
                }
            }
        }
        $errorData = buildResponseMessage ( $messages, SystemCode::BAD_REQUEST );
        return response ()->json ( $errorData );
    }
}
