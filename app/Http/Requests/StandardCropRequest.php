<?php 

namespace Gis\Http\Requests;
use Gis\Models\SystemCode;
use Gis\Http\Requests\Request;


class StandardCropRequest extends Request {
	

	
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
 			'crops_id' => 'required',
 			'fertilization_standard_amount_n'=>'required',
 			'fertilization_standard_amount_p'=>'required',
 			'fertilization_standard_amount_k'=>'required'
		];
	}

	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages() {
		return array (
			'crops_id.required' => trans('common.standardcropinfo_crop_required'),
			'fertilization_standard_amount_n.required' => trans('common.standardcropinfo_n_required'),
			'fertilization_standard_amount_p.required' => trans('common.standardcropinfo_p_required'),
			'fertilization_standard_amount_k.required' => trans('common.standardcropinfo_k_required')				
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
