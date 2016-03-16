<?php
namespace Gis\Models\Services;

use Gis\Helpers\DataHelper;
use Gis\Models\Entities\FolderLayer;
use Gis\Models\Repositories\FertilityMapFacade;
use Gis\Models\Repositories\FertilityMapSelectionFacade;
use Gis\Models\Repositories\FertilityMapSelectionInfoFacade;
use Gis\Models\Repositories\FertilizerMapInfoFacade;
use Gis\Models\Repositories\FertilizerMapPaymentFacade;
use Gis\Models\Repositories\FertilizerMapPropertyFacade;
use Gis\Models\Repositories\FolderFacade;
use Gis\Exceptions\GisException;
use Gis\Models\SystemCode;
use Gis\Models\Entities\User;
use Gis\Models\Repositories\UserFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

/**
 * Management all business of folder layer object.
 * Class FolderService
 *
 * @package Gis\Models\Services
 */
class FolderService extends BaseService implements FolderServiceInterface
{

    /**
     *
     * @var LAYER_TERRAIN_LIMIT
     */
    const LAYER_TERRAIN_LIMIT = 2;

    /**
     *
     * @var FOLDER_TYPE_ADMIN
     */
    const FOLDER_TYPE_TERRAIN = 'terrain';

    /**
     *
     * @var FOLDER_TYPE_ADMINISTRATOR
     */
    const FOLDER_TYPE_ADMIN = 'admin';

    /**
     *
     * @var FOLDER_TYPE_FERTILITY
     */
    const FOLDER_TYPE_FERTILITY = 'fertility';

    /**
     *
     * @var FOLDER_TYPE_FERTILIZER
     */
    const FOLDER_TYPE_FERTILIZER = 'fertilizer';

    /**
     *
     * @var FOLDER_TYPE_BIN
     */
    const FOLDER_TYPE_BIN = 'bin';

    /**
     *
     * @var TERRAIN_SCALE_25000
     */
    const TERRAIN_SCALE_25000 = 1;

    /**
     *
     * @var TERRAIN_SCALE_50000
     */
    const TERRAIN_SCALE_50000 = 2;

    /**
     *
     * @var FOLDER_TYPE
     */
    const FOLDER_TYPE = 'folder';

    /**
     *
     * @var LAYER_TYPE
     */
    const LAYER_TYPE = 'layer';

    /**
     *
     * @var TYPE_FERTILIZER
     */
    const TYPE_FERTILIZER = 'fertilizer';

    /**
     *
     * @var TYPE_FERTILITY
     */
    const TYPE_FERTILITY = 'fertility';

    /**
     * Get all Folders & layers
     *
     * @param $inVisibleLayer the
     *            boolean value in order to get visible or all the layers
     * @return array
     */
    public function getFolderLayers($inVisibleLayer)
    {
        $result = array();

        $folders = FolderFacade::orderBy('order_number', 'asc')->get();
        foreach ($folders as $folder) {
            if ($inVisibleLayer === "false") {
                if ($folder->is_invisible_layer === true) {
                    continue;
                }
            }
            $liAttr = array(
                'data-order' => $folder->order_number,
                'data-toggle' => 'tooltip',
                'title' => $folder->name
            );
            if ($folder->parent_folder && $folder->is_fertilizer_folder && $folder->fertilizerMap) {
                $liAttr['fertilizer_map_id'] = $folder->fertilizerMap->id;
            }

            $name = $folder->name;
            $result[] = array(
                'id' => $folder->id,
                'parent' => $folder->parent_folder ? $folder->parent_folder : '#',
                'text' => $name,
                'type' => $this->getTypeFolderLayer($folder),
                'li_attr' => $liAttr
            );
        }

        return $result;
    }

    /**
     * Get Type folder & layer for tree view
     *
     * @param FolderLayer $item
     *
     * @return String $type
     */
    function getTypeFolderLayer(FolderLayer $item)
    {
        $prefix = $item->parent_folder ? self::LAYER_TYPE . '_' : self::FOLDER_TYPE . '_';
        $suffix = '';

        if ($item->is_fertility_folder) {
            $type = self::TYPE_FERTILITY;
        } elseif ($item->is_fertilizer_folder) {
            $type = self::TYPE_FERTILIZER;
        } elseif ($item->is_terrain_folder) {
            $type = self::FOLDER_TYPE_TERRAIN;
        } elseif ($item->is_recyclebin) {
            $type = self::FOLDER_TYPE_BIN;
        } else {
            $type = self::FOLDER_TYPE_ADMIN;
        }

        if ($item->parent_folder) {
            $suffix = $item->is_invisible_layer ? '_hidden' : '';
        }

        return $prefix . $type . $suffix;
    }

    /**
     * Get layer's id of user
     *
     * @param User $user
     *
     * @return Array $layerIds
     */
    function getLayerIdsByUser(User $user)
    {
        $layerIds = array();
        $fertilizerMaps = $user->fertilizer_maps;
        $fertilityMaps = $user->fertility_maps;

        if (! $fertilityMaps->isEmpty()) {
            foreach ($fertilityMaps as $item) {
                $layer = $item->folderLayer;
                array_push($layerIds, $layer->id);
            }
        }
        if (! $fertilizerMaps->isEmpty()) {
            foreach ($fertilizerMaps as $item) {
                $layer = $item->folderLayer;
                array_push($layerIds, $layer->id);
            }
        }
        return $layerIds;
    }

    /**
     * Get Folders & layers by role & type
     *
     * @param User|Gis\Models\Entities\User $user
     * @param boolean $inVisibleLayer
     * @param
     *            $session_id
     * @return array() Gis\Models\Entities\FolderLayer
     */
    public function getFolderLayersByUser(User $user, $inVisibleLayer, $session_id)
    {
        $group = $user->usergroup;
        $folders = $group->folders;

        if (! $folders->isEmpty()) {
            $userLayerIds = $this->getLayerIdsByUser($user);
            $result = array();
            $tmpFolder = [];
            $tmpLayer = [];

            foreach ($folders as $folder) {
                if(!in_array($folder->id,$tmpFolder)){
                    $result[] = array(
                        'id' => $folder->id,
                        'parent' => '#',
                        'text' => $folder->name,
                        'type' => $this->getTypeFolderLayer($folder),
                        'li_attr' => array(
                            'data-order' => $folder->order_number
                        )
                    );
                    array_push($tmpFolder,$folder->id);
                }

                $layers = $folder->layers;
                if (! $layers->isEmpty()) {
                    foreach ($layers as $layer) {

                        if (! in_array($layer->id, $userLayerIds)) {
                            if (! $layer->is_terrain_folder)
                                continue;
                        }

                        if ($group->is_guest_group == true) {
                            if (($layer->is_fertilizer_folder) && ($layer->session_id != $session_id))
                                continue;
                        }

                        if ($inVisibleLayer === "false") {
                            if ($layer->is_invisible_layer === true)
                                continue;
                        }

                        $liAttr = array(
                            'data-order' => $layer->order_number,
                            'data-toggle' => 'tooltip',
                            'title' => $layer->name
                        );

                        if ($layer->is_fertilizer_folder) {
                            $liAttr['fertilizer_map_id'] = $layer->fertilizerMap->id;
                        }
                        if(!in_array($layer->id,$tmpLayer)){
                            $result[] = array(
                                'id' => $layer->id,
                                'parent' => $folder->id,
                                'text' => $layer->name,
                                'type' => $this->getTypeFolderLayer($layer),
                                'li_attr' => $liAttr
                            );
                            array_push($tmpLayer,$layer->id);
                        }

                    }
                }
            }
            return $result;
        }
    }

    /**
     * Filter folder export with conditions
     *
     * @param FolderLayer $folder
     *
     * @return boolean
     */
    public function filterExportFolders(FolderLayer $folder)
    {
        if ($folder->is_recyclebin) {
            return false;
        }

        if ($folder->is_terrain_folder) {
            return false;
        }

        return true;
    }

    /**
     * Change Layer's folder
     * switch Layer to new Folder
     *
     * @param array $postData
     * @return mixed
     * @throws GisException
     */
    public function changeMapLayer($postData)
    {
        $layer = $this->findById($postData['layerId'], self::LAYER_TYPE);
        $folder = $this->findById($postData['folderId']);

        $updateData = array(
            'parent_folder' => $postData['folderId']
        );
        if ($folder->is_terrain_folder || $folder->is_fertility_folder || $folder->is_fertilizer_folder) {
            $updateData['is_terrain_folder'] = $folder->is_terrain_folder;
            $updateData['is_recyclebin'] = $folder->is_recyclebin;
            $updateData['is_fertility_folder'] = $folder->is_fertility_folder;
            $updateData['is_fertilizer_folder'] = $folder->is_fertilizer_folder;
            $updateData['is_admin_folder'] = $folder->is_admin_folder;
            $updateData['old_parent_folder']=$postData['folderId'];
        }
        else if($folder->is_recyclebin){
            $updateData['old_parent_folder']=$layer['parent_folder'];
        }
        $updateData = $this->modifyData($updateData);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_LAYER, $updateData);
        return FolderFacade::update($updateData, $layer->id);
    }

    /**
     * Create new a folder
     *
     * @param
     *            $data
     * @return mixed
     */
    public function createFolderLayer($data)
    {
        $folder = $this->modifyData($data, true);
        $folder = FolderFacade::create($data);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_FOLDER, $folder);
        return $folder->id;
    }

    /**
     * Get Folder types
     *
     * @return array()
     */
    public function getFolderTypes()
    {
        return array(
            '' => trans('common.folder_create_type_default'),
            self::FOLDER_TYPE_ADMIN => '管理者用フォルダ',
            self::FOLDER_TYPE_FERTILITY => '肥沃度マップ',
            self::FOLDER_TYPE_FERTILIZER => '可変施肥マップ',
            self::FOLDER_TYPE_TERRAIN => '地形図',
            self::FOLDER_TYPE_BIN => 'ゴミ箱',


        );
    }

    /**
     * Create new Folder
     *
     * @param array $postData
     * @return boolean
     */
    function createFolder($postData)
    {
        if (FolderFacade::checkCreateDataExists('name', $postData['name'], 'ilike')) {
            throw new GisException(trans('common.folder_create_exists'), SystemCode::CONFLICT);
        }

        if ($postData['folderType'] == self::FOLDER_TYPE_BIN) {
            if (FolderFacade::checkCreateDataExists('is_recyclebin', true)) {
                throw new GisException(trans('common.folder_create_bin_exists'), SystemCode::CONFLICT);
            }
        }

        if ($postData['folderType'] == self::FOLDER_TYPE_TERRAIN) {
            $terrainFolder=FolderFacade::selectModel()->where('is_terrain_folder',true)->where('parent_folder',null)->first();
            if ($terrainFolder) {
                throw new GisException(trans('common.folder_create_terrain_exists'), SystemCode::CONFLICT);
            }
        }
        $maxOrder = $this->getMaxFolderOrder();

        $attributes = array(
            'name' => $postData['name'],
            'order_number' => $maxOrder + 1,
            'is_recyclebin' => $postData['folderType'] == self::FOLDER_TYPE_BIN ? true : false,
            'is_terrain_folder' => $postData['folderType'] == self::FOLDER_TYPE_TERRAIN ? true : false,
            'is_fertility_folder' => $postData['folderType'] == self::FOLDER_TYPE_FERTILITY ? true : false,
            'is_fertilizer_folder' => $postData['folderType'] == self::FOLDER_TYPE_FERTILIZER ? true : false,
            'is_admin_folder' => $postData['folderType'] == self::FOLDER_TYPE_ADMIN ? true : false
        );
        $attributes = $this->modifyData($attributes, true);
        $newFolder = FolderFacade::create($attributes);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_FOLDER, $newFolder);
        $folderGroupDatas = array();
        foreach ($postData['groupId'] as $groupId) {
            $folderGroupDatas[] = $this->modifyData(array(
                'user_group_id' => $groupId
            ), true);
        }

        return $newFolder->userGroups()->attach($folderGroupDatas);
    }

    /**
     * Get Max Folder ordinal number
     *
     * @return int
     */
    function getMaxFolderOrder()
    {
        return FolderFacade::getMaxFolderOrder();
    }

    /**
     * Get Max Folder Layer ordinal number
     *
     * @return int
     */
    function getMaxFolderLayerOrder()
    {
        return FolderFacade::getMaxFolderLayerOrder();
    }

    /**
     * Fond Folder by Id
     *
     * @param int $id
     * @param String $type
     * @return Gis\Models\Entities\FolderLayer
     */
    function findById($id, $type = self::FOLDER_TYPE)
    {
        $resource = FolderFacade::findById($id);

        if (empty($resource)) {
            $keyMessage = $type == self::FOLDER_TYPE ? 'common.folder_edit_not_exists' : 'common.layer_edit_not_exists';
            throw new GisException(trans($keyMessage), SystemCode::NOT_FOUND);
        }

        return $resource;
    }

    /**
     * Get Folder Type value
     *
     * @param Gis\Models\Entities\FolderLayer $folder
     * @return String
     */
    function getFolderTypeValue(FolderLayer $folder)
    {
        if ($folder->is_terrain_folder)
            $typeSelected = FolderService::FOLDER_TYPE_TERRAIN;
        elseif ($folder->is_recyclebin)
            $typeSelected = FolderService::FOLDER_TYPE_BIN;
        elseif ($folder->is_fertility_folder)
            $typeSelected = FolderService::FOLDER_TYPE_FERTILITY;
        elseif ($folder->is_fertilizer_folder)
            $typeSelected = FolderService::FOLDER_TYPE_FERTILIZER;
        else
            $typeSelected = FolderService::FOLDER_TYPE_ADMIN;

        return $typeSelected;
    }

    /**
     * Get User group Selected
     *
     * @param Gis\Models\Entities\FolderLayer $folder
     * @return array() $result
     */
    function getUserGroupValues(FolderLayer $folder)
    {
        $result = array();
        $userGroups = $folder->userGroups;

        if (! $userGroups->isEmpty()) {
            foreach ($userGroups as $group) {
                array_push($result, $group->id);
            }
        }
        return $result;
    }

    /**
     * Get List Folder Orders
     *
     * @return array() $result
     */
    function getListOrders()
    {
        $result = array();
        $folders = FolderFacade::getListOrders();
        if (! $folders->isEmpty()) {
            foreach ($folders as $folder) {
                $result[$folder->order_number] = $folder->order_number;
            }
        }
        return $result;
    }

    /**
     * Update Folder data
     *
     * @param Gis\Models\Entities\FolderLayer $folder
     * @param array $postData
     * @return boolean
     */
    function updateFolder(FolderLayer $folder, $postData, $operator = '=')
    {
        $attributes = array();
        if (! empty($postData['is_invisible_layer'])) {
            $attributes['is_invisible_layer'] = $postData['is_invisible_layer'];
        }

        if (! empty($postData['name'])) {
            if (FolderFacade::checkEditDataExists('name', $postData['name'], $folder, 'ilike')) {
                throw new GisException(trans('common.folder_create_exists'), SystemCode::CONFLICT);
            }
            $attributes['name'] = $postData['name'];
        }

        if (! empty($postData['parent_folder'])) {
            if (! FolderFacade::checkCreateDataExists('id', $postData['parent_folder'])) {
                throw new GisException(trans('common.folder_edit_parent_not_exists'), SystemCode::CONFLICT);
            }
            $attributes['parent_folder'] = $postData['parent_folder'];
        }

        if (! empty($postData['folderType'])) {
            if ($postData['folderType'] == self::FOLDER_TYPE_BIN) {
                if (FolderFacade::checkEditDataExists('is_recyclebin', true, $folder)) {
                    throw new GisException(trans('common.folder_create_bin_exists'), SystemCode::CONFLICT);
                }
                $attributes['is_recyclebin'] = $postData['folderType'] == self::FOLDER_TYPE_BIN ? true : false;
            } elseif ($postData['folderType'] == self::FOLDER_TYPE_TERRAIN) {
                if (FolderFacade::checkEditDataExists('is_terrain_folder', true, $folder)) {
                    throw new GisException(trans('common.folder_create_terrain_exists'), SystemCode::CONFLICT);
                }
                $attributes['is_terrain_folder'] = $postData['folderType'] == self::FOLDER_TYPE_TERRAIN ? true : false;
            }
        }

        if (! empty($postData['order_number'])) {
            if ($folder->order_number !== $postData['order_number']) {
                $folderOrders = FolderFacade::getListOrders();
                $count = 0;
                $oldOrder = $folder->order_number;

                foreach ($folderOrders as $item) {
                    if ($item->id == $folder->id)
                        $item->order_number = $postData['order_number'];
                    $count ++;
                }

                $start = $postData['order_number'] > $oldOrder ? $oldOrder : $postData['order_number'];
                $end = $postData['order_number'] > $oldOrder ? $postData['order_number'] : $oldOrder;

                foreach ($folderOrders as $item) {
                    if ($item->id == $folder->id || $item->order_number < $start || $item->order_number > $end)
                        continue;

                    $item->order_number = $postData['order_number'] > $oldOrder ? $item->order_number - 1 : $item->order_number + 1;
                    $item->save();
                }
            }

            $attributes['order_number'] = $postData['order_number'];
        }

        if (! empty($attributes))
            $attributes = $this->modifyData($attributes);
        if (! empty($postData['groupId'])) {
            FolderFacade::update($attributes, $folder->id);
            $folderGroupDatas = array();
            foreach ($postData['groupId'] as $groupId) {
                $folderGroupDatas[] = $this->modifyData(array(
                    'user_group_id' => $groupId
                ), true);
            }
            if(is_null($folder->parent_folder))
            {
                //logging for folder
                ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_FOLDER, $folder);
            }
            else{
                //logging for layer
                ApplicationLogFacade::logActionMode2 ( LoggingAction::ACTION_UPDATE_LAYER, $folder);
            }

            return $folder->userGroups()->sync($folderGroupDatas);
        } else {
            if(is_null($folder->parent_folder))
            {
                //logging for folder
                ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_FOLDER, $folder);
            }
            else{
                //logging for layer
                ApplicationLogFacade::logActionMode2 ( LoggingAction::ACTION_UPDATE_LAYER, $folder);
            }

            return FolderFacade::update($attributes, $folder->id);
        }

    }

    /**
     * Get All Scale type of Layer Terrain
     *
     * @return array()
     */
    public function getScaletypes()
    {
        return array(
            '' => trans('common.folder_terain_create_scale_default'),
            self::TERRAIN_SCALE_25000 => '25,000',
            self::TERRAIN_SCALE_50000 => '50,000'
        );
    }

    /**
     * Create new Layer Terrain
     *
     * @param array $postData
     * @return boolean
     */
    function createLayer($postData)
    {
        if (! FolderFacade::checkCreateDataExists('is_terrain_folder', true)) {
            throw new GisException(trans('common.folder_terrain_not_exists'), SystemCode::NOT_FOUND);
        }

        if (FolderFacade::checkCreateDataExists('scale_type', $postData['scaleType'])) {
            throw new GisException(trans('common.terrain_scale_exists'), SystemCode::CONFLICT);
        }
        if (FolderFacade::checkCreateDataExists('name', $postData['name'], 'ilike')) {
            throw new GisException(trans('common.folder_create_exists'), SystemCode::CONFLICT);
        }
        $folderTerrain = FolderFacade::getFolderTerrain();
        $attributes = array(
            'name' => $postData['name'],
            'is_recyclebin' => false,
            'is_terrain_folder' => true,
            'scale_type' => $postData['scaleType'],
            'parent_folder' => $folderTerrain->id
        );
        $attributes = $this->modifyData($attributes, true);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_TERRAIN_LAYER, $attributes);
        return $newFolder = FolderFacade::create($attributes);
    }

    /**
     * Delete Folders From Db
     *
     * @param array $postData
     * @return boolean
     */
    function deleteFolders($postData)
    {
        $folderIds = $postData['folderIds'];
        $this->validateBeforeDeleteFolders($folderIds);
        $folders = FolderFacade::getFolderByIds($folderIds);

        foreach ($folders as $folder) {
            $folder->userGroups()->detach();
            $folder->delete();
        }
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DELETE_FOLDER, $folderIds);
        return true;
    }

    /**
     * Delete Layers & map From Db
     *
     * @param array $postData
     * @return boolean
     */
    function deleteLayers($postData)
    {
        $layerIds = $postData['folderIds'];
        $layers = FolderFacade::getFolderByIds($layerIds);
        $binFolder = $this->getFolderBytype(self::FOLDER_TYPE_BIN);
        if (empty($binFolder))
            throw new GisException(trans('common.layer_delete_need_bin'), SystemCode::NOT_FOUND);
        DB::transaction(function () use ($layers, $binFolder) {
            foreach ($layers as $layer) {
                $folder = $layer->folder;
                if (!$folder->is_recyclebin && !session('user')->usergroup->is_guest_group) {
                    $layer->old_parent_folder=$layer->parent_folder;
                    $layer->parent_folder = $binFolder->id;
                    $layer->update();
                    ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_MOVE_LAYER_TO_BIN, $layer);
                    continue;
                }
                $fertilityMap = $layer->map;
                $fertilityMapExtend = $layer->fertilityMapExtend;
                if ($fertilityMapExtend) {
                    $fertilityMapExtend->delete();
                }
                if ($fertilityMap) {
                    $fertilityMapInfos = $fertilityMap->mapinfo;
                    if (!$fertilityMapInfos->isEmpty()) {
                        foreach ($fertilityMapInfos as $fertilityMapInfo) {
                            $fertilityMapInfo->delete();
                        }
                    }
                    $fertilityMapSelections = $fertilityMap->fertilityMapSelection;
                    if ($fertilityMapSelections) {
                        foreach ($fertilityMapSelections as $fertilityMapSelection) {
                            $fertilityMapSelectionInfos = $fertilityMapSelection->fertilityMapSelectionInfo;
                            if ($fertilityMapSelectionInfos) {
                                foreach ($fertilityMapSelectionInfos as $fertilityMapSelectionInfo) {
                                    $fertilityMapSelectionInfo->delete();
                                }
                                $fertilityMapSelection->delete();
                            }
                        }
                    }

                    $fertilizerMaps = $fertilityMap->fertilizerMap;
                    if (!$fertilizerMaps->isEmpty()) {
                        throw new GisException(trans('common.fertility_maps_using'), SystemCode::CONFLICT);
                    }
                    $fertilityMap->delete();
                    ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_DELETE_FERTILITY_MAP, $layer);
                } else {
                    $fertilizerMap = $layer->fertilizerMap;
                    if ($fertilizerMap) {
                        $fertilizerMapInfos = $fertilizerMap->fertilizerMapInfo;
                        if (!$fertilizerMapInfos->isEmpty()) {
                            foreach ($fertilizerMapInfos as $fertilizerMapInfo) {
                                $fertilizerMapInfo->delete();
                            }
                        }
                        $fertilizerProperty = $fertilizerMap->fertilizerMapProperty;
                        if ($fertilizerProperty) {
                            $fertilizerProperty->delete();
                        }
                        $fertilizerMapPayments = $fertilizerMap->fertilizerMapPayment;
                        if ($fertilizerMapPayments) {
                            foreach ($fertilizerMapPayments as $fertilizerMapPayment) {
                                $fertilizerMapPayment->delete();
                            }
                        }

                        $fertilizerStages = $fertilizerMap->fertilizerStage;
                        if (!$fertilizerStages->isEmpty()) {
                            foreach ($fertilizerStages as $fertilizerStage)
                                $fertilizerStage->delete();
                        }

                        $fertilizerMap->delete();
                        ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_DELETE_FERTILIZER_MAP, $fertilizerMap);
                    }
                }
                $layer->delete();
            }
        });
        return true;
    }

    /**
     * Validate before Delete folders
     *
     * @param array $folderIds
     * @return boolean
     */
    function validateBeforeDeleteFolders($folderIds)
    {
        $countExists = FolderFacade::countByIds($folderIds);
        if ($countExists > count($folderIds))
            throw new GisException(trans('common.folder_delete_not_found'), SystemCode::NOT_FOUND);
        $countLayers = FolderFacade::countFolderContainLayerByIds($folderIds);
        if ($countLayers)
            throw new GisException(trans('common.folder_delete_layers_exists'), SystemCode::NOT_FOUND);

        return true;
    }

    /**
     * Check Folder Type exists
     *
     * @param String $type
     * @return boolean
     */
    function checkFolderTypeExists($type)
    {
        switch ($type) {
            case self::FOLDER_TYPE_BIN:
                return FolderFacade::checkCreateDataExists('is_recyclebin', true);
            case self::FOLDER_TYPE_TERRAIN:
                return FolderFacade::checkCreateDataExists('is_terrain_folder', true);
            default:
                return (FolderFacade::checkCreateDataExists('is_recyclebin', false) && FolderFacade::checkCreateDataExists('is_terrain_folder', false));
        }
    }

    /**
     * Check Limit layer terrain
     *
     * @return boolean
     */
    function isLimitTerrain()
    {
        $totalTerrain = FolderFacade::countLayerTerrain();
        if ($totalTerrain >= self::LAYER_TERRAIN_LIMIT)
            throw new GisException(trans('common.layer_terrain_limit'), SystemCode::CONFLICT);
        return true;
    }

    /**
     * Check Terrain (Folder & Layer)
     *
     * @param int $id
     * @param String $type
     * @return boolean
     */
    public function checkTerrain(FolderLayer $folder, $type = self::FOLDER_TYPE)
    {
        if ($type == self::FOLDER_TYPE) {
            if ($folder->is_terrain_folder)
                return true;
        } else {
            if ($folder->scale_type)
                return true;
        }

        return false;
    }

    /**
     * Get Folder By Type
     *
     * @param String $type
     * @return Gis\Models\Entities\FolderLayer
     */
    public function getFolderBytype($type = self::FOLDER_TYPE_ORTHER)
    {
        switch ($type) {
            case self::FOLDER_TYPE_BIN:
                $field = 'is_recyclebin';
                break;
            case self::FOLDER_TYPE_TERRAIN:
                $field = 'is_terrain_folder';
                break;
            default:
                if ($field == self::FOLDER_TYPE_ADMIN)
                    $field = 'is_admin_folder';
                elseif ($field == self::FOLDER_TYPE_FERTILITY)
                    $field = 'is_fertility_folder';
                else
                    $field = 'is_fertilizer_folder';
        }

        $queryBuilder = FolderFacade::whereConditions(array(
            array(
                $field,
                '=',
                true
            )
        ));

        return $field == 'is_terrain_folder' || $field == 'is_recyclebin' ? $queryBuilder->first() : $queryBuilder->get();
    }

    /**
     * Create Map Layer
     *
     * @param String $field
     * @param Int $userId
     * @param String $layerName
     *
     * @return Gis\Models\Entities\FolderLayer
     */
    public function createLayerMap($type, $userId, $layerName = null)
    {
        session_start();
        $user = UserFacade::findByField('id', $userId)->first();
        if (empty($user))
            throw new GisException(trans('common.user_not_exists'), SystemCode::NOT_FOUND);

        $fertilizerFolder = $this->getRandomParentFolder($type, $user->usergroup->id);
        if (empty($fertilizerFolder))
            throw new GisException(trans('common.create_fertilizer_parent_not_found'), SystemCode::NOT_FOUND);

        $insertData = array(
            'order_number' => $this->getMaxFolderLayerOrder() + 1,
            'name' => empty($layerName) ? $type . '_' . date('Y_m_d_H_i_s') : $layerName,
            'parent_folder' => $fertilizerFolder->id,
            'is_admin_folder' => $fertilizerFolder->is_admin_folder,
            'is_terrain_folder' => $fertilizerFolder->is_terrain_folder,
            'is_fertilizer_folder' => $fertilizerFolder->is_fertilizer_folder,
            'is_fertility_folder' => $fertilizerFolder->is_fertility_folder,
            'is_recyclebin' => $fertilizerFolder->is_recyclebin,
            'session_id' => $user->usergroup->is_guest_group ? session_id() : null
        );

        $insertData = $this->modifyData($insertData, true);
        $newLayer = FolderFacade::create($insertData);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_LAYER, $newLayer);
        return $newLayer;
    }

    /**
     * Get Random folder parent by Type & group
     *
     * @param String $type
     * @param Int $userGroupId
     *
     * @return Gis\Models\Entities\FolderLayer
     */
    public function getRandomParentFolder($type, $userGroupId)
    {
        switch ($type) {
            case self::FOLDER_TYPE_ADMIN:
                $field = 'is_admin_folder';
                break;
            case self::FOLDER_TYPE_BIN:
                $field = 'is_recyclebin';
                break;
            case self::FOLDER_TYPE_FERTILITY:
                $field = 'is_fertility_folder';
                break;
            case self::FOLDER_TYPE_FERTILIZER:
                $field = 'is_fertilizer_folder';
                break;
            default:
                $field = 'is_terrain_folder';
                break;
        }

        $fertilizerFolder = FolderFacade::getFolderByGroup($field, $userGroupId);
        return $fertilizerFolder;
    }

    /**
     * User request an order for fertilizer map
     *
     * @param
     *            $postData
     * @return mixed
     */
    public function createFertilityMapPayment($postData)
    {
        $attributes['fertilizer_id'] = $postData['fertilizer_id'];
        $attributes['download_date'] = $postData['download_date'];
        $attributes['user_code'] = $postData['user_code'];
        $attributes['unit_price'] = $postData['unit_price'];
        $attributes['area'] = $postData['area'];
        $attributes['download_id'] = $postData['download_id'];
        if($postData['is_paid']){
            $attributes['payment_date'] = $postData['payment_date'];
            $attributes['is_paid'] = $postData['is_paid'];
        }
        $attributes['crops_id'] = $postData['crops_id'];
        $attributes = $this->modifyData($attributes, true);
        return FertilizerMapPaymentFacade::create($attributes);

    }

    /**
     * Get User's Fertility Map
     *
     * @param int $userId
     *
     * @return array() $fertilityMaps
     */
    public function getUserFertilityMaps($userId)
    {
        $user = UserFacade::findByField('id', $userId)->first();
        if (empty($user))
            throw new GisException(trans('common.authorization_user_not_exists'), SystemCode::NOT_FOUND);

        if ($user->usergroup->auth_authorization) {
            $userId = null;
        }

        $fertilityMaps[''] = trans('common.select_item_null');
        $maps = FolderFacade::getUserLayerByType('is_fertility_folder', $userId);

        foreach ($maps as $item) {
            $fertilityMap = $item->map;
            if ($fertilityMap)
                $fertilityMaps[$fertilityMap->id . '_' . $item->id] = $item->name;
        }
        return $fertilityMaps;
    }

    /**
     * get coordinate system number
     * @param $layerId
     * @return mixed
     * @throws GisException
     */
    public function getCoordinateSystemNumber($layerId)
    {
        $layer = $this->findById($layerId, self::LAYER_TYPE);
        $fertilityMap = $layer->fertilizerMap;
        $fertility = FertilityMapFacade::findById($fertilityMap->fertility_map_id);
        return $fertility->coordinates_system_number;
    }

    /**
     * get fertilizer usual amount
     * @param $layerId
     * @return mixed
     * @throws GisException
     */
    public function getFertilizerUsual($layerId)
    {
        $layer = $this->findById($layerId, self::LAYER_TYPE);
        $fertilizerMap = $layer->fertilizerMap;
        return FertilizerMapPropertyFacade::findByField('fertilizer_map_id', $fertilizerMap->id)->first();
    }

    /**
     * get fertilizer
     * @param $layerId
     * @return mixed
     * @throws GisException
     */
    public function getFertilizer($layerId)
    {
        $layer = $this->findById($layerId, self::LAYER_TYPE);
        $fertilizerMap = $layer->fertilizerMap;
        $fertilizer = FertilizerMapInfoFacade::findByField('fertilizer_id', $fertilizerMap->id)->all();

        return $fertilizer;
    }

    /**
     * get geo for fertilizer
     * @param $layerId
     * @return mixed
     * @throws GisException
     */
    public function getGeoFertilizer($layerId)
    {
        $layer = $this->findById($layerId, self::LAYER_TYPE);
        $fertilityMap = DB::table('fertility_maps')->find($layer->fertilizerMap->fertility_map_id);
        $queryTemplate ="select concat(ST_ExportToCsv(geo, %s)::text,',',trunc(round(main_fertilizer))::text,',',trunc(round(sub_fertilizer))::text) as json, ST_Ymin(geo) as ymin
		from fertilizer_map_infos
		where fertilizer_id= %s
		group by id order by ymin;";
        $rawQuery = sprintf($queryTemplate,DataHelper::getCoordinates($fertilityMap->coordinates_system_number),
            $layer->fertilizerMap->id);
        return DB::select($rawQuery);
    }

    /**
     * get geo for fertility selection
     * @param $layerId
     * @return array
     */
    public function getGeoFertility($layerId)
    {
        $layer = $this->findById($layerId, self::LAYER_TYPE);
        $fertilityMap = DB::table('fertility_maps')->find($layer->fertilizerMap->fertility_map_id);
        $data = array();
        $query = DB::table('fertilizer_maps')
            ->select(DB::raw(sprintf(' ST_AsGeoJson(1,st_transform(geo,%s),0,0) as json ',DataHelper::getCoordinates($fertilityMap->coordinates_system_number))))
            ->where('layer_id','=', $layerId)
            ->get();
        if($query){
            array_push($data, $query[0]->json);
        }
        else{
            array_push($data,"");
        }

        return $data;
    }

    /**
     * get layer name
     * @param $layerId
     * @return mixed
     */
    public function getNameLayer($layerId)
    {
        return FolderFacade::findById($layerId)->name;
    }

    /**
     * update fertilizer map is ready for condition of fertility map
     * @param $fertilizerId
     */
    public function updateIsReady($fertilizerId){
        FertilityMapSelectionFacade::updateIsReady($fertilizerId);
    }
}