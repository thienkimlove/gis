<?php
namespace Gis\Http\Controllers;

use Carbon\Carbon;
use Gis\Exceptions\GisException;
use Gis\Http\Requests\BuyFertilizerMapRequest;
use Gis\Http\Requests\CreateFolderRequest;
use Gis\Http\Requests\CreateLayerTerrainRequest;
use Gis\Http\Requests\DeleteFolderRequest;
use Gis\Models\Repositories\FertilityMapFacade;
use Gis\Models\Repositories\FertilizerMapFacade;
use Gis\Models\Repositories\FolderFacade;
use Gis\Models\Repositories\GroupFacade;
use Gis\Models\Services\FertilizerServiceFacade;
use Gis\Models\Services\FolderService;
use Gis\Models\Services\FolderServiceFacade;
use Gis\Models\Services\UserServiceFacade;
use Gis\Models\SystemCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Gis\Helpers\AESEncryption\MCryptAES256Implementation;
use Gis\Helpers\AESEncryption\AESCryptFileLib;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

class MainController extends CoreController
{

    /**
     * If no login, this will be redirect to login page
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = UserServiceFacade::findUserById(session('user')->id);
        $state = UserServiceFacade::getUserState(session('user')->user_code);
        $user_fertility=FertilityMapFacade::findByField('user_id',session('user')->id)->first();
        $isAdmin = UserServiceFacade::findGroupById(session('user')->user_group_id);
        $firstTimeLogin = false;

        $config = [
            'current_user_id' => $user->id,
            'is_admin' =>  ($user->usergroup->auth_authorization) ? 1 : 0
        ];
        if($state!=null){
            $config['state'] = [
                'last_active_layer_id' => $state->last_active_layer_id
            ];
        }
        else if($user_fertility==null){
            $config['state'] = [
                'last_active_layer_id' => 0
            ];
        }
        else{
            $config['state'] = [
                'last_active_layer_id' => null
            ];
        }
        session_start();
        if($state==null && $isAdmin->auth_authorization==false)
            $firstTimeLogin = true;
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_OPEN_INITIAL_SCREEN,"");
        return view('admin.index',compact('config','firstTimeLogin'));
    }

    /**
     * Get all the folders & layers that belong to specified user
     * @param Request $request
     * @return mixed
     */
    public function getTree(Request $request)
    {
        session_start();
        $inVisibleLayer = $request->get('isVisibleLayer');
        $user_id=$request->get('user_id');
        if($user_id=='')
            $user=UserServiceFacade::findUserById(session('user')->id);
        else $user = UserServiceFacade::findUserById($user_id);
        $session_id=session_id();
        if ($user->usergroup->auth_authorization) {
            $folders = FolderServiceFacade::getFolderLayers($inVisibleLayer);

        } else {
            $folders = FolderServiceFacade::getFolderLayersByUser($user,$inVisibleLayer,$session_id);

        }

        return $folders;
    }

    /**
     * Save the user's actions in order to show the user's action when login again
     * @param Request $request
     * @return mixed
     */
    public function updateStateOfUser(Request $request)
    {
        $postData = $request->all();
        $user_code= session('user')->user_code;
        $postData['user_code']= $user_code;
        UserServiceFacade::updateStateOfUser($postData, $user_code);
    }

    /**
     * User downloads the fertilizer map
     * @param BuyFertilizerMapRequest $request
     * @return mixed
     */
    public function fertilizerMapPayment(Request $request)
    {
        $data = $request->all();
        $array = explode("a", $data['area']);
        FertilizerServiceFacade::createFertilityMapPayment($this->buildPaymentData($data['layer_id'],$data['download_id'],$array[0]));
        $responseData = buildResponseMessage(trans('common.fertilizer_map_buy_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * User performs logout action
     * @return mixed
     */
    public function logout()
    {
        Session::flush('user');
        return redirect('/');
    }

    /**
     * Show the form for creating a new folder
     *
     * @return Response
     */
    public function create()
    {
        $folderTypes = FolderServiceFacade::getFolderTypes();
        $userGroups = UserServiceFacade::getArrayGroups();
        return view('admin.folders.create', compact('folderTypes', 'userGroups'));
    }

    /**
     * Create new a folder and save its information into the database
     * @param CreateFolderRequest $request
     * @return mixed
     */
    public function store(CreateFolderRequest $request)
    {
        $postData = $request->all();
        FolderServiceFacade::createFolder($postData);

        $responseData = buildResponseMessage(trans('common.folder_create_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Show the form for editing the specified folder
     *
     * @param int $id the id of the folder in database
     * @return Response
     */
    public function edit($id)
    {
        $folder = FolderServiceFacade::findById($id);

        $folderTypes = FolderServiceFacade::getFolderTypes();
        $typeSelected = FolderServiceFacade::getFolderTypeValue($folder);
        $groupSelected = FolderServiceFacade::getUserGroupValues($folder);
        $listOrders = FolderServiceFacade::getListOrders();

        $userGroups = UserServiceFacade::getArrayGroups();
        return view('admin.folders.edit', compact('listOrders', 'folder', 'folderTypes', 'userGroups', 'typeSelected', 'groupSelected'));
    }

    /**
     * Update the folder information to the database
     * @param CreateFolderRequest $request
     * @param $id the id of folder in the database
     * @return the message that indicates the processing is successful or not
     */
    public function update(CreateFolderRequest $request, $id)
    {
        $folder = FolderServiceFacade::findById($id);
        $postData = $request->all();
        FolderServiceFacade::updateFolder($folder, $postData);

        $responseData = buildResponseMessage(trans('common.folder_edit_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Change the parent folder for specified layer
     * @param Request $request the request to change parent info
     * @return the message that indicates the processing is successful or not
     */
    public function changeFolder(Request $request)
    {
        $postData = $request->all();
        FolderServiceFacade::changeMapLayer($postData);
        $responseData = buildResponseMessage(trans('common.folder_edit_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Create new a terrain layer
     * @return Response
     * @throws GisException will be thrown if the terrain folder doesn't exit
     */
    public function createLayer()
    {
        if (! FolderServiceFacade::checkFolderTypeExists(FolderService::FOLDER_TYPE_TERRAIN))
            throw new GisException(trans('common.folder_terrain_not_exists'), SystemCode::NOT_FOUND);

        FolderServiceFacade::isLimitTerrain();
        $scaleTypes = FolderServiceFacade::getScaletypes();
        return view('admin.folders.createLayer', compact('scaleTypes'));
    }

    /**
     * User performs to download the fertilizer map
     * @param $id the id of fertilizer to download
     * @return the fertilizer information
     */
    public function buyFertilizerView($layerId,$numberMesh)
    {
        $layer=FolderFacade::findById($layerId);
        $date= Carbon::now();
        $data= FertilizerServiceFacade::getBuyFertilizerData($layerId);
        if($numberMesh==0){
            //get area of fertilizer map not same crop or year
            $numberMesh = FertilizerServiceFacade::getAreaSelection($layerId);
            $area = ($numberMesh).'a';
        }else if($numberMesh < 0){
            //get area of fertilizer map have record in payment
            $area = $data['area'].'a';
        }else
            //get area of fertilizer map same crop and year, different mesh selection
            $area = ($numberMesh).'a';
        $unitPrice=$data['unitPrice'];
        $total = round(($area*$unitPrice)/10);
        $total = number_format($total);
        $unitPrice=number_format($unitPrice);
        $id = $data['id'];
        $cropName = $data['cropName'];
        $meshSize = $data['meshSize'].'m';
        return view('admin.users.buy_fertilizer_view',compact('layer','date','area','cropName','id','unitPrice','total','meshSize'));
    }

    /**
     * Create new a terrain layer
     * @param CreateLayerTerrainRequest $request the request to create terrain layer
     * @return the message that indicates the processing is successful or not
     */
    public function storeLayer(CreateLayerTerrainRequest $request)
    {
        $postData = $request->all();
        FolderServiceFacade::createLayer($postData);

        $responseData = buildResponseMessage(trans('common.folder_terrain_create_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Show the form for editing the layer
     * @param $id the layer id to edit
     * @return the form for editing layer
     */
    public function editLayer($id)
    {
        $folder = FolderServiceFacade::findById($id, FolderService::LAYER_TYPE);
        return view('admin.folders.editLayer', compact('folder'));
    }

    /**
     * Update information for a folder
     * @param CreateLayerTerrainRequest $request
     * @param $id the folder id to be updated
     * @return the message that indicates the processing is successful or not
     */
    public function updateLayer(CreateLayerTerrainRequest $request, $id)
    {
        $folder = FolderServiceFacade::findById($id, FolderService::LAYER_TYPE);
        $postData = $request->all();
        FolderServiceFacade::updateFolder($folder, $postData);

        $responseData = buildResponseMessage(trans('common.folder_terrain_edit_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Remove the folder and its children from the database
     * @param DeleteFolderRequest $request
     * @return the message that indicates the processing is successful or not
     */
    public function deleteFolder(DeleteFolderRequest $request)
    {
        $postData = $request->all();
        if ($postData['isFolderSelected'] === 'true') {
            FolderServiceFacade::deleteFolders($postData);
            $respMessage = trans('common.folder_delete_success_message');
        } else {
            FolderServiceFacade::deleteLayers($postData);
            $respMessage = trans('common.layer_delete_success_message');
        }

        $responseData = buildResponseMessage($respMessage, SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Restore the folder and its children from bin to old folder
     * @param post data
     * @return the message that indicates the processing is successful or not
     */
    public function layerRestore( $id)
    {
        $layer=FolderFacade::find($id);
        $layer->update(['parent_folder'=>$layer->old_parent_folder]);
        return buildResponseMessage(null, SystemCode::SUCCESS);
    }

    /**
     * Implement the auto complete function to retrieve user information
     * @param Request $request
     * @return mixed
     */
    public function getAutocompleteUsers(\Illuminate\Http\Request $request){
        $postData= $request->input('keyword');
        $users = UserServiceFacade::findUserByKeyword($postData);
        return response()->json($users);
    }

    /**
     * Download data from db with file CSV
     */
    public function downloadFileCsv($id)
    {
        $encrypt= (trans('common.encryptFileCSV')=="true")?true :false ;
        $layerName = FolderServiceFacade::getNameLayer($id);
        $name = str_replace(" ","_",$layerName);
        $formatFile = trans("common.format_file");
        //Add application log
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DOWNLOAD_FERTILIZER_MAP, "layer id=".$id);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: text/csv");
        header("Content-Description: File Transfer");
        if($encrypt) {
            header("Content-Disposition: attachment; filename=" . $name . ".csv." . $formatFile);
        }
        else{
            header("Content-Disposition: attachment; filename=" . $name . ".csv" );
        }
        header("Expires: 0");
        header("Pragma: public");
        $CSN = FolderServiceFacade::getCoordinateSystemNumber($id);
        $fertilizerUsual = FolderServiceFacade::getFertilizerUsual($id);
        if(!$fertilizerUsual->sub_fertilizer_usual_amount)
            $subFertilizerUsual = 0;
        else $subFertilizerUsual = $fertilizerUsual->sub_fertilizer_usual_amount;
        $dataGeoFertilizer = FolderServiceFacade::getGeoFertilizer($id);
        $dataGeoFertility = FolderServiceFacade::getGeoFertility($id);
        $strVal= $this->getPolygonDataAndIgnoreLastItem(json_decode($dataGeoFertility[0])->coordinates[0]);
        $fileNameIn =sprintf('temp%s%s.csv',DIRECTORY_SEPARATOR,FertilizerServiceFacade::generateCode());
        $fileNameOut =sprintf('temp%s%s.csv.aes',DIRECTORY_SEPARATOR,FertilizerServiceFacade::generateCode());
        $fin = fopen(public_path($fileNameIn),'w');
        $format = "%s\r\n";
        // Put the data into the stream
        $csvContent = sprintf($format,$CSN);
        $csvContent = $csvContent.
            sprintf($format,number_format($fertilizerUsual->main_fertilizer_usual_amount,0,"","").",".
                number_format($subFertilizerUsual,0,"",""));
        $csvContent = $csvContent.
            sprintf($format,$strVal);
        foreach($dataGeoFertilizer as $key){
            $csvContent = $csvContent.sprintf($format,$key->json);
        }
        fwrite($fin, $csvContent);
        fclose($fin);
        if($encrypt) {
            $mcrypt = new MCryptAES256Implementation();
            $lib = new AESCryptFileLib($mcrypt);
            $lib->encryptFile(public_path($fileNameIn), trans('common.key_AES'), public_path($fileNameOut));
            readfile(public_path($fileNameOut));
            unlink(public_path($fileNameIn));
            unlink(public_path($fileNameOut));
        }
        else {
            readfile(public_path($fileNameIn));
            unlink(public_path($fileNameIn));
        }
    }

    /**
     * Get polygon string selection area
     * @param $arrPolygon
     * @return string
     */
    private function getPolygonDataAndIgnoreLastItem($arrPolygon){
        $strVal="";
        $i = 0;
        $len = count($arrPolygon);
        foreach($arrPolygon as $item){
            $item = array_reverse($item);
            if ($i == $len - 1){
                //ignore last item due to it's identical last item
                break;
            }
            if($strVal ==""){
                $strVal = $strVal.implode(",",$item);
            }else{
                $strVal = $strVal.",".implode(",",$item);
            }
            $i++;
        }
        return $strVal;
    }

    /**
     * the download popup will be displayed for user to download encrypted csv file
     * @param $layerId
     * @return int
     * if return value >= 0: display download popup
     * if return value =-1: do not display download popup and download file immediately
     */
    public function downloadFertilizerMap($layerId){
        $arrShowPopup = FertilizerServiceFacade::canShowPopup($layerId);
        $user = UserServiceFacade::findUserById(session('user')->id);
        $userGroup = UserServiceFacade::findGroupById($user->user_group_id);
        if($userGroup->auth_authorization)
            $arrShowPopup["canShowPopup"] = true;
        if($arrShowPopup["canShowPopup"] == false)
        {
            //have record, fertilizer map is paid, do not open popup to download, just download immediately
            $this->saveDataPaymentRecord($layerId,$arrShowPopup["unpaidMesh"]);
        }
        return $arrShowPopup;
    }

    /**
     * Create new a payment record whenever download a fertilizer map
     * Use this function when download fertilizer map immediately
     * @param $layerId the layer id that associates with this fertilizer map
     * @return mixed
     */
    function saveDataPaymentRecord($layerId,$area){

        $downloadId = FertilizerServiceFacade::generateCode();
        FertilizerServiceFacade::createFertilityMapPayment($this->buildPaymentData($layerId,$downloadId,$area));
        $user = UserServiceFacade::findUserById(session('user')->id);
        $state = UserServiceFacade::getUserState(session('user')->user_code);

        $config = [
            'current_user_id' => $user->id,
            'is_admin' =>  ($user->usergroup->auth_authorization) ? 1 : 0
        ];
        if($state!=null){
            $config['state'] = [
                'is_invisible_scalebar' => $state->is_invisible_scalebar,
                'is_invisible_zoom_toolbar' => $state->is_invisible_zoom_toolbar,
                'is_invisible_legend' => $state->is_invisible_legend,
                'last_active_layer_id' => $state->last_active_layer_id
            ];
        }
        else{
            $config['state'] = [
                'last_active_layer_id' => null
            ];
        }
        return view('admin.index',compact('config'));
    }

    /**
     * Build payment data
     * @param $layerId
     * @param $area
     * @return mixed
     */
    private function buildPaymentData($layerId,$downloadId,$area){
        $fertilizer_map = FertilizerMapFacade::findByLayerId($layerId);
        $data = FertilizerServiceFacade::getBuyFertilizerData($layerId);
        $postData['fertilizer_id'] = $fertilizer_map->id;
        $postData['download_date'] = Carbon::now();
        $postData['user_code'] = session('user')->user_code;
        if(!is_null($data) && !is_null($data['unitPrice']))
            $postData['unit_price'] = $data['unitPrice'];
        else
            $postData['unit_price']=0;
        $postData['download_id'] = $downloadId;
        $postData['area'] = $area;
        $postData['is_paid'] = false;
        $postData['crops_id']= FertilizerServiceFacade::getCropIdFertilizerMap($layerId);
        return $postData;
    }

    /**
     * Reload current page
     * @return mixed
     */
    public function reload(){
        return view('admin.blank');
    }
}