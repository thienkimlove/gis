<?php
namespace Gis\Http\Controllers;

use Gis\Http\Controllers\Controller;
use Gis\Models\Services\FolderServiceFacade;
use Gis\Models\Services\UserServiceFacade;
use Gis\Http\Requests\CreateFolderRequest;
use Gis\Models\SystemCode;
use Gis\Exceptions\GisException;
use Gis\Models\Services\FertilityMapServiceFacade;
use Gis\Http\Requests\CreateLayerTerrainRequest;
use Gis\Models\Services\FolderService;
use Illuminate\Http\Request;
use Gis\Http\Requests\DeleteFolderRequest;

/**
 * Use this class to handle all the businesses regarding the Folder and layer
 * Class FolderController
 * @package Gis\Http\Controllers
 */
class FolderController extends Controller
{

    /**
     * Show the list of folders and layers
     * @return the list of folders and layers
     */
    public function index()
    {
        $maps = FertilityMapServiceFacade::getAllAdminMaps();
        $folders = htmlspecialchars(json_encode(FolderServiceFacade::getFolderLayers(true)));
        return view('admin.folders.index')->with('maps', $maps)->with('folders', $folders);
    }

    /**
     * Show the form to create new a folder
     * @return list of folder types and list of groups to create new a folder
     */
    public function create()
    {
        $folderTypes = FolderServiceFacade::getFolderTypes();
        $userGroups = UserServiceFacade::getArrayGroups();
        unset($userGroups['']);
        return view('admin.folders.create', compact('folderTypes', 'userGroups'));
    }

    /**
     * Create new a folder
     * @param CreateFolderRequest $request the information of folder to create
     * @return the message that indicates the processing is successful or not
     */
    public function store(CreateFolderRequest $request)
    {
        $postData = $request->all();
        FolderServiceFacade::createFolder($postData);
        
        $responseData = buildResponseMessage(trans('common.folder_create_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Show the form to edit a folder
     * @param $id the id of folder to edit
     * @return the folder information
     */
    public function edit($id)
    {
        $folder = FolderServiceFacade::findById($id);
        
        $folderTypes = FolderServiceFacade::getFolderTypes();
        $typeSelected = FolderServiceFacade::getFolderTypeValue($folder);
        $groupSelected = FolderServiceFacade::getUserGroupValues($folder);
        $listOrders = FolderServiceFacade::getListOrders();
        
        $userGroups = UserServiceFacade::getArrayGroups();
        unset($userGroups['']);
        return view('admin.folders.edit', compact('listOrders', 'folder', 'folderTypes', 'userGroups', 'typeSelected', 'groupSelected'));
    }

    /**
     * Update the folder information
     * @param CreateFolderRequest $request the information of folder
     * @param $id the folder id
     * @return the message that indicates the updating is successful or not
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
     * Change the parent folder for layer
     * @param Request $request the layer information to change its parent
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
     * @return the form to create new a terrain layer
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
     * Create new a layer
     * @param CreateLayerTerrainRequest $request the layer information to create
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
     *
     * @param int $id the layer id to edit
     * @return the layer information
     */
    public function editLayer($id)
    {
        $folder = FolderServiceFacade::findById($id, FolderService::LAYER_TYPE);
        return view('admin.folders.editLayer', compact('folder'));
    }

    /**
     * Update the layer information
     * @param CreateLayerTerrainRequest $request the layer information to edit
     * @param $id the id of parent folder
     * @return the message that indicates the processing is successful or not
     */
    public function updateLayer(CreateLayerTerrainRequest $request, $id)
    {
        $folder = FolderServiceFacade::findById($id, FolderService::FOLDER_TYPE);
        $postData = $request->all();
        FolderServiceFacade::updateFolder($folder, $postData, 'ilike');
        $responseData = buildResponseMessage(trans('common.folder_terrain_edit_success_message'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Remove the folder from the database
     * @param DeleteFolderRequest $request the folder information to delete
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
}
