<?php namespace Gis\Http\Requests;

use Gis\Models\SystemCode;

class HelpLinkRequest extends Request {

	/**
	 *
	 * @var MAX_LENGTH_CONTENT
	 */
	const MAX_LENGTH_HELP_ADDRESS_URL = 200;

	/**
	 *
	 * @var MAX_LENGTH_VERSION
	 */
	const MAX_LENGTH_HELP_DESTINATION = 200;
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
				'address' => 'required|max:' . self::MAX_LENGTH_HELP_ADDRESS_URL,
				'help' => 'required|max:' . self::MAX_LENGTH_HELP_DESTINATION
		);
	}
	
	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages() {
		return array (
				'address.required' => trans ( 'common.helplink_required_page_address_url_field' ),
				'address.max' => trans ( 'common.helplink_check_max_field' ),
				'help.required' => trans ( 'common.helplink_required_help_destination_field' ),
				'help.max' => trans ( 'common.helplink_check_max_field' )
		);
	}

    /**
     * handler after validate request.
     *
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
		return response ()->json ( buildResponseMessage ( $messages, SystemCode::BAD_REQUEST ) );
	}
}
