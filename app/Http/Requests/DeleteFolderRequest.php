<?php
namespace Gis\Http\Requests;

use Gis\Http\Requests\Request;
use Gis\Models\SystemCode;

class DeleteFolderRequest extends Request
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
        return array(
            'folderIds' => 'required',
        );
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array(
            'folderIds.required' => trans('common.folder_edit_ids_required'),
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
