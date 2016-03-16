<?php 

namespace Gis\Http\Requests;
use Gis\Models\SystemCode;
use Gis\Http\Requests\Request;


class ForgetPasswordRequest extends Request {
	

	
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
			'email' => 'required'
		];
	}

	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages() {
		return array (
				'email.required' => trans ( 'common.forgetpassword_email_required' )
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
