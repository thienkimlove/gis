<?php
namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;
use Gis\Models\SystemCode;

class CreateLayerTerrainRequest extends Request
{

    /**
     *
     * @var LAYER_NAME_LENGTH_MAX
     */
    const LAYER_NAME_LENGTH_MAX = 100;

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
    public function rules(\Illuminate\Http\Request $request)
    {
        $rules = array(
            'name' => 'required|max:' . self::LAYER_NAME_LENGTH_MAX
        );
        $folderId = $request->get('folderId');
        
        if (empty($folderId))
            $rules['scaleType'] = 'required';
        
        return $rules;
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array(
            'name.required' => trans('common.folder_terrain_create_name_required'),
            'name.max' => trans('common.folder_terrain_create_name_max'),
            'scaleType.required' => trans('common.folder_terrain_create_scale_type_required')
        );
    }

    /**
     * handler after validate request.
     *
     * @return array
     */
    public function response(array $validateErrors)
    {
        $messages = array();
        
        if (! empty($validateErrors)) {
            foreach ($validateErrors as $fieldErrors) {
                foreach ($fieldErrors as $errorMessage) {
                    array_push($messages, $errorMessage);
                }
            }
        }
        $errorData = buildResponseMessage($messages, SystemCode::BAD_REQUEST);
        return response()->json($errorData);
    }
}
