<?php namespace Gis\Http\Controllers;


use Gis\Models\Services\FertilityMapServiceFacade;
use Gis\Models\Services\FertilityMapServiceInterface;
use Gis\Services\Logging\ApplicationLogInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Gis\Models\Services\FolderServiceFacade;
use Illuminate\Support\Facades\Response;

class UploadController extends Controller
{

    private $_applicationLog;
    private $fertilityMapService;
    private $url;

    /**
     * the constructor to initialize new instance of this class
     * @param ApplicationLogInterface $applicationLog
     * @param FertilityMapServiceInterface $fertilityMapService
     */
    function __construct(ApplicationLogInterface $applicationLog, FertilityMapServiceInterface $fertilityMapService)
    {
        $this->_applicationLog = $applicationLog;
        $this->fertilityMapService = $fertilityMapService;
    }


    /**
     * Show the upload fertility form to update csv file
     * @return mixed
     */
    public function index()
    {
        return view('admin.import.upload');
    }

    /**
     *Retrieve the fertility map base on the condition
     * @param Request $request
     * @param null $query
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterFertilityMap(Request $request, $query = null)
    {
        $pagingRequest = $request->all();
        $postData = json_decode($query, true);
        $responses = $this->fertilityMapService->filterFertilityMap($postData, $pagingRequest);
        return response()->json($responses);

    }

    /**
     *  Process save change map'layers
     * @param Request $request
     */
    public function processExport(Request $request) {
        $postData = $request->all();
        FolderServiceFacade::changeMapLayer($postData);
    }



    /**
     * Remove the specified fertility map
     * @param  int $id
     * @return Response
     */
    public function destroy($id = null)
    {
        $list = Input::get('user_delete_list_data');
        $ids = unserialize($list);
        $rows = FertilityMapServiceFacade::deleteFertilityMap($ids);
        $message = is_integer($rows) ? trans('common.user_delete_usermap_success') . ":" . $rows : $rows;
        return Redirect::route('upload.layer')->with('message', $message);


    }

}
