<?php namespace Gis\Http\Requests;

use Gis\Models\SystemCode;

class FooterRequest extends Request {

	/**
	 *
	 * @var MAX_LENGTH_CONTENT
	 */
	const MAX_LENGTH_CONTENT = 300;

	/**
	 *
	 * @var MAX_LENGTH_VERSION
	 */
	const MAX_LENGTH_VERSION = 50;
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
		return array (
				'content' => 'required|max:' . self::MAX_LENGTH_CONTENT,
				'version' => 'required|max:' . self::MAX_LENGTH_VERSION
		);
	}
	
	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages() {
		return array (
				'content.required' => trans ( 'common.footer_required_content_field' ),
				'content.max' => trans ( 'common.footer_check_content_input_max' ),
				'version.required' => trans ( 'common.footer_required_version_field' ),
				'version.max' => trans ( 'common.footer_check_version_input_max' )
		);
	}

    /**
     * handler after validate request.     *
     * @param array $validateErrors
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
