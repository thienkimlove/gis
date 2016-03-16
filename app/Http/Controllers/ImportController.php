<?php namespace Gis\Http\Controllers;

use Gis\Http\Requests;
use Gis\Http\Requests\ImportDataRequest;
use Gis\Http\Requests\ImportLayerMapRequest;
use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\FolderLayer;
use Gis\Models\Repositories\FolderFacade;
use Gis\Models\Services\FertilityMapServiceInterface;
use Gis\Models\Services\FolderService;
use Gis\Models\Services\FolderServiceFacade;
use Gis\Models\Services\FolderServiceInterface;
use Gis\Models\Services\UserService;
use Gis\Models\Services\UserServiceInterface;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Gis\Models\Services\UserServiceFacade;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

/**
 * Use this class to handle fertility csv file
 * Class ImportController
 * @package Gis\Http\Controllers
 */
class ImportController extends CoreController
{

    private $fertilityMapService;
    private $userService;
    private $folderServiceInterface;
    public function __construct( FertilityMapServiceInterface $fertilityMapService,UserServiceInterface $userService,FolderServiceInterface $folderServiceInterface) {
        $this->fertilityMapService = $fertilityMapService;
        $this->userService = $userService;
        $this->folderServiceInterface = $folderServiceInterface;
    }


    /**
     * Show the form to upload fertility data
     * @return the view to upload fertility data
     */
    public function index()
    {
        $users = $this->userService->getUserWithNormal();
        $folders = FolderFacade::getFolderNotBinAndNotTerrain();
            return view( 'admin.import.import', compact( 'users' , 'folders') );
    }


    /**
     * Save the uploaded fertility data to the database
     * @param ImportDataRequest $request
     * @return mixed
     */
    public function store( ImportDataRequest $request )
    {

        $file = Input::file( 'file_csv' );
        $userId = Input::get( 'user_id' );
        $fileName = Input::get( 'map_name' );
        $folder_id = Input::get( 'folder_id' );
        $status = $this->fertilityMapService->importLayer( $file, $userId, $fileName, $folder_id );
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::MODE2_UPLOAD_FERTILITY_MAP,$file);
        if(empty($status)) {
            return redirect()->back()->withErrors( ['message'=>trans( 'common.lbl_import_layer_map_with_file_csv_error' )] );
        }elseif(!empty($status['message'])){
            return redirect()->back()->withErrors( ['errors' => $status['message'] ] );
        }
        return redirect()->back()->with( 'status', true );


    }


    /**
     * Implement the auto complete function to get the user
     * @param \Illuminate\Http\Request $request the search condition to get user
     * @return the list of users that matched with search condition
     */
    public function ajaxAutocomplete(\Illuminate\Http\Request $request){
        $postData= $request->input('keyword');
        $users = UserServiceFacade::findUserByKeyword($postData);
        return response()->json($users);
    }

}
