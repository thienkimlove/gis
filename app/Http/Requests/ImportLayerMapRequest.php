<?php

namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;

class ImportLayerMapRequest extends Request
{
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
        return [
            'fertility_id' => 'required',
            'folder_id' => 'required',
            'layer_name' => 'required',
        ];
    }

    /**
     *  Set custom messages for validator errors.
     * @return array
     */
    public function messages() {
        return array (
            'fertility_id.required' => trans ( 'common.lbl_import_layer_map_to_folder_map_name_error' ),
            'folder_id.required' => trans ( 'common.lbl_import_layer_map_to_folder_folder_name_error' ),
            'layer_name.required' => trans ( 'common.lbl_import_layer_map_to_folder_layer_name_error' ),
        );
    }
}
