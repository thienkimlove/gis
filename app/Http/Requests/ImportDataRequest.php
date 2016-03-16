<?php namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;

class ImportDataRequest extends Request
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'map_name' => 'required|max:100',//|max:50
            'user_id' => 'required',
            'folder_id' => 'required',
        ];

    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'map_name.required' => trans('common.usermap_import_data_map_name_required'),
            'map_name.max' => trans('common.usermap_import_data_map_name_max'),
            'map_name.unique' => trans('common.usermap_import_data_map_name_unique'),
            'map_name.exists' => trans('common.usermap_import_data_map_name_unique'),
            'user_id.required' => trans('common.usermap_import_data_user_id_required'),
            'folder_id.required' => trans('common.usermap_import_data_folder_id_required'),
        ];
    }


}
