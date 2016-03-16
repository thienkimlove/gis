<?php

namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;
use Illuminate\Validation\Factory;
use Gis\Models\SystemCode;

class AuthenticateRequest extends Request {
	/**
	 *
	 * @var MAX_LENGTH_USERNAME
	 */
	const MAX_LENGTH_USERNAME = 20;
	
	/**
	 *
	 * @var MAX_LENGTH_PASSWORD
	 */
	const MAX_LENGTH_PASSWORD = 30;
	
	/**
	 *
	 * @var MIN_LENGTH_PASSWORD
	 */
	const MIN_LENGTH_PASSWORD = 8;
	
	/**
	 * Define custom rules
	 *
	 * @param Illuminate\Validation\Factory $factory        	
	 */
	public function __construct(Factory $factory) {
		$factory->extend ( 'test', function ($attribute, $value, $parameters) {
			return false;
		}, 'Bad number format' );
	}
	
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
				'password' => 'required|alpha_num|max:' . self::MAX_LENGTH_PASSWORD . '|min:' . self::MIN_LENGTH_PASSWORD 
		);
	}
	
	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages() {
		return array (
				'username.required' => trans ( 'common.login_username_required' ),
				'username.alpha_num' => trans ( 'common.login_username_alpha_num' ),
				'username.max' => trans ( 'common.login_username_max' ),
				'password.required' => trans ( 'common.login_password_required' ),
				'password.alpha_num' => trans ( 'common.login_password_alpha_num' ),
				'password.max' => trans ( 'common.login_password_max' ),
				'password.min' => trans ( 'common.login_password_min' ) 
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
