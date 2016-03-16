<?php
namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;
use Gis\Models\SystemCode;

class CreateFolderRequest extends Request
{

    /**
     *
     * @var SEARCH_MAX_LENGTH_USERNAME
     */
    const FOLDER_NAME_LENGTH_MAX = 100;

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
        $rules = array();
        $postData = $request->all();
        
        if (empty($postData['sortAble'])) {
            if (! empty($postData['folderId'])) {
                $rules['groupId'] = 'required';
            } else {
                $rules = array(
                    'folderType' => 'required',
                    'groupId' => 'required',
                    'name' => 'required|max:' . self::FOLDER_NAME_LENGTH_MAX
                );
            }
        }
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
            'name.required' => trans('common.folder_create_name_required'),
            'name.max' => trans('common.folder_create_name_max'),
            'folderType.required' => trans('common.folder_create_type_required'),
            'groupId.required' => trans('common.folder_create_group_required')
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
