<?php
namespace Gis\Models\Services;

use Gis\Models\Entities\FolderLayer;
use Gis\Models\Entities\User;

/**
 * Using for declaring methods list for database business layer.
 * Interface FolderServiceInterface
 *
 * @package Gis\Models\Services
 */
interface FolderServiceInterface extends BaseServiceInterface
{

    /**
     * define function Get Folders & layers by role & type
     *
     * @return array() Gis\Models\Entities\FolderLayer
     */
    public function getFolderLayers($inVisibleLayer);

    /**
     * define function Get Folders & layers by role & type
     *
     * @param User|Gis\Models\Entities\User $user            
     * @param boolean $inVisibleLayer            
     * @param
     *            $session_id
     * @return array() Gis\Models\Entities\FolderLayer
     */
    public function getFolderLayersByUser(User $user, $inVisibleLayer, $session_id);

    /**
     * define function Get Type folder & layer for tree view
     *
     * @param FolderLayer $item            
     *
     * @return String $type
     */
    function getTypeFolderLayer(FolderLayer $item);

    /**
     * define function Get layer's id of user
     *
     * @param User $user            
     *
     * @return Array $layerIds
     */
    function getLayerIdsByUser(User $user);

    /**
     * define function Filter folder export with conditions
     *
     * @param FolderLayer $folder            
     *
     * @return boolean
     */
    public function filterExportFolders(FolderLayer $folder);

    /**
     *
     * @return id
     */
    public function createFolderLayer($data);

    /**
     * define function Change map's layer
     * switch map to new layer
     *
     * @param array() $postData            
     * @return boolean
     */
    public function changeMapLayer($postData);

    /**
     * Define method Get Folder types
     *
     * @return array()
     */
    public function getFolderTypes();

    /**
     * Define method Create new Folder
     *
     * @param array $postData            
     * @return boolean
     */
    function createFolder($postData);

    /**
     * Define method Get Max Folder ORder
     *
     * @return int
     */
    function getMaxFolderOrder();

    /**
     * Define method Fond Folder by Id
     *
     * @param int $id            
     * @return Gis\Models\Entities\FolderLayer
     */
    function findById($id);

    /**
     * Define method Get Folder Type value
     *
     * @param Gis\Models\Entities\FolderLayer $folder            
     * @return String
     */
    function getFolderTypeValue(FolderLayer $folder);

    /**
     * Define method Get User group Selected
     *
     * @param Gis\Models\Entities\FolderLayer $folder            
     * @return array() $userGroups
     */
    function getUserGroupValues(FolderLayer $folder);

    /**
     * Get List Folder Orders
     *
     * @return array() $result
     */
    function getListOrders();

    /**
     * Define methodGet All Scale type of Layer Terrain
     *
     * @return array()
     */
    public function getScaletypes();

    /**
     * Define method Create new Layer Terrain
     *
     * @param array $postData            
     * @return boolean
     */
    function createLayer($postData);

    /**
     * Define method Update Folder data
     *
     * @param Gis\Models\Entities\FolderLayer $folder            
     * @param array $postData            
     * @return boolean
     */
    function updateFolder(FolderLayer $folder, $postData);

    /**
     * Define method Delete Folders From Db
     *
     * @param array $postData            
     * @return boolean
     */
    function deleteFolders($postData);

    /**
     * Define method Validate before Delete folders
     *
     * @param array $folderIds            
     * @return boolean
     */
    function validateBeforeDeleteFolders($folderIds);

    /**
     * Define method Delete Layers & map From Db
     *
     * @param array $postData            
     * @return boolean
     */
    function deleteLayers($postData);

    /**
     * Define method Check Folder Type exists
     *
     * @param String $type            
     * @return boolean
     */
    function checkFolderTypeExists($type);

    /**
     * Define method Check Limit layer terrain
     *
     * @return boolean
     */
    function isLimitTerrain();

    /**
     * Define method Get Folder By Type
     *
     * @param String $type            
     * @return Gis\Models\Entities\FolderLayer
     */
    public function getFolderBytype($type = self::FOLDER_TYPE_ORTHER);

    /**
     * Define method Create Map Layer
     *
     * @param String $field            
     * @return Gis\Models\Entities\FolderLayer
     */
    public function createLayerMap($type, $userId, $layerName = null);

    /**
     * Define method Get Random folder parent by Type & group
     *
     * @param String $type            
     * @param Int $userGroupId            
     *
     * @return Gis\Models\Entities\FolderLayer
     */
    public function getRandomParentFolder($type, $userGroupId);

    /**
     *
     * @param
     *            $postData
     * @return mixed
     */
    public function createFertilityMapPayment($postData);

}
