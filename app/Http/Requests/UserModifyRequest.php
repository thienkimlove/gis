<?php

namespace Gis\Http\Requests;

use Gis\Models\SystemCode;

class UserModifyRequest extends Request {
	/**
	 *
	 * @var SEARCH_MAX_LENGTH_USERNAME
	 */
	const SEARCH_MAX_LENGTH_USERNAME = 20;
	
	/**
	 *
	 * @var MAX_LENGTH_EMAIL
	 */
	const MAX_LENGTH_EMAIL = 50;
	
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
				'username' => 'required|max:' . AuthenticateRequest::MAX_LENGTH_USERNAME,
				'password' => 'alpha_num|max:' . AuthenticateRequest::MAX_LENGTH_PASSWORD,
				'email' => 'required|email_not_hashed',
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
				'password.alpha_num' => trans ( 'common.user_registration_password_alpha_num' ),
				'password.max' => trans ( 'common.user_registration_password_max' ),
				'email.required' => trans ( 'common.user_registration_email_required' ),
				'email.email_not_hashed' => trans ( 'common.user_registration_email_email' ),
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
