<?php namespace Gis\Http\Requests;

use Gis\Models\Services\SecurityFacade;

class GroupRequest extends Request {

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
			'group_name' => 'required',
			'description' => 'required',
		];
	}

}
