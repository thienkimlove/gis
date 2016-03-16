<?php
namespace Gis\Models\Repositories;

use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\FolderLayer;

/**
 * User repository provider functional access to database.It like same data provider layer.
 * Class FolderRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FolderRepositoryEloquent extends GisRepository implements FolderRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FolderLayer';
    }

    /**
     * Get All folders of application
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllFolders()
    {
        return $this->orderBy('order_number', 'asc')->get();
    }

    /**
     * Get User folders of application
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getUserFolders()
    {
        return $this->orderBy('order_number', 'asc')->get();
    }

    /**
     *
     * @return list
     */
    public function getFolderNotBinAndNotTerrain()
    {
        $results_admin = $this->model->where('parent_folder', '=', null)
            ->where('is_recyclebin', '=', false)
            ->whereIn('is_terrain_folder', array(
            null,
            false
        ))
            ->where('is_admin_folder', '=', true)
            ->get();
        return $results_admin;
    }

    /**
     * Check Create Data exists In DB
     *
     * @param String $field            
     * @param String $value            
     *
     * @return boolean
     */
    public function checkCreateDataExists($field, $value, $operator = '=',$folder=null)
    {
        if($field=='name') {
            if($folder==null){
                $datas = $this->model->where($field, $operator, $value)->get();
                if (count($datas) != 0) {
                    foreach ($datas as $data) {
                        if($data->parent_folder==null) return true;
                    }
                }
            }
            else if ($folder->parent_folder!=null) {
                if($folder->is_terrain_folder) {
                    if ($this->model->where($field, $operator, $value)->whereNotNull('parent_folder')->first()) return true;
                }
                else {
                    $datas = $this->model->where($field, $operator, $value)->get();
                    if (count($datas) != 0) {
                        foreach ($datas as $data) {
                            if($data->scale_type != null) return true;
                            else if ($data->fertilizerMap) {
                                if ($folder->fertilizerMap) {
                                    if ($data->fertilizerMap->user_id == $folder->fertilizerMap->user_id) return true;
                                } else {
                                    if ($data->fertilizerMap->user_id == $folder->map->user_id) return true;
                                }
                            }
                            else if ($data->map) {
                                if ($folder->map) {
                                    if ($data->map->user_id == $folder->map->user_id) return true;
                                } else {
                                    if ($data->map->user_id == $folder->fertilizerMap->user_id) return true;
                                }
                            }
                        }
                    }
                }
            }
            else{
                $datas = $this->model->where($field, $operator, $value)->get();
                if (count($datas) != 0) {
                    foreach ($datas as $data) {
                        if($data->parent_folder==null) return true;
                    }
                }
            }
        }
        else if ($this->model->where($field, $operator, $value)->exists()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check Edit Data exists In DB
     *
     * @param String $field            
     * @param String $value            
     * @param FolderLayer|Gis\Models\Entities\FolderLayer $user            
     *
     * @param
     *            $operator
     * @return boolean
     */
    public function checkEditDataExists($field, $value, FolderLayer $folder, $operator = '=')
    {
         if ($this->checkCreateDataExists($field, $value, $operator,$folder)) {
            if (strtolower($folder->$field) != strtolower($value))
                return true;
        }
        return false;
    }

  /**
     * Check Edit Data exists In DB
     *
     * @param String $field
     * @param String $value
     * @param FolderLayer|Gis\Models\Entities\FolderLayer $user
     *
     * @param
     *            $operator
     * @return boolean
     */
    public function checkEditDataExistsForLayer($field, $value, FolderLayer $user, $operator = '=')
    {
        //check whether layer name is existent or not
        //layer name must be different for one user
        //but it can be equal other one of other user
        $userId = session('user')->id;
        $queryTemplate = "select count(*) from folderlayers
                        where (id in (select layer_id from fertility_maps where user_id = %s)
                        or id in (select layer_id from fertilizer_maps where user_id = %s))
                        and name != '%s'
                        and parent_folder is not null
                        and id != %s";
        $rawQuery = sprintf($queryTemplate,$userId,$userId,$value,$user->id);
        $resutl = DB::select($rawQuery);
        if ( is_null($resutl) && count($resutl)>0
        ) {
            return true;
        }
        return false;
    }
    /**
     * Get Max Folder ORder
     *
     * @return int
     */
    function getMaxFolderOrder()
    {
        $maxOrderNumber = $this->model->whereNull('parent_folder')->max('order_number');
        return $maxOrderNumber;
    }

    /**
     * Get Max Folder Layer ORder
     *
     * @return int
     */
    function getMaxFolderLayerOrder()
    {
        $maxOrderNumber = $this->model->max('order_number');
        return $maxOrderNumber;
    }

    /**
     * Find folder by Id
     *
     * @param int $id            
     *
     * @return Gis\Models\Entities\FolderLayer $user
     */
    public function findById($id)
    {
        return $this->findByField('id', $id)->first();
    }

    /**
     * Get Collection Folder order asc
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    function getListOrders()
    {
        return FolderLayer::select(array(
            'id',
            'order_number',
            'name'
        ))->orderBy('order_number', 'asc')->get();
    }

    /**
     * Get Folder By Type
     *
     * @return Gis\Models\Entities\FolderLayer
     */
    function getFolderTerrain()
    {
        return FolderLayer::where('is_terrain_folder', true)->whereNull('parent_folder')->first();
    }

    /**
     * Check Folder exists from ids
     *
     * @param array() $ids            
     *
     * @return int
     */
    public function countByIds($ids)
    {
        return FolderLayer::select(array(
            'id'
        ))->whereIn('id', $ids)->count();
    }

    /**
     * Count layers with folder ids
     *
     * @param array() $ids            
     *
     * @return int
     */
    public function countFolderContainLayerByIds($ids)
    {
        return FolderLayer::WhereNotNull('parent_folder')->whereIn('parent_folder', $ids)->count();
    }

    /**
     * get Folder from ids
     *
     * @param array() $ids            
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getFolderByIds($ids)
    {
        return FolderLayer::whereIn('id', $ids)->get();
    }

    /**
     * Define method count layer terrain
     *
     * @return boolean
     */
    function countLayerTerrain()
    {
        return FolderLayer::WhereNotNull('parent_folder')->WhereNotNull('scale_type')->count();
    }

    /**
     * Get Random Folder By Type & Group
     *
     * @param String $type            
     * @param Int $userGroupId            
     *
     * @return Gis\Models\Entities\FolderLayer $folderLayer
     */
    function getFolderByGroup($type, $userGroupId)
    {
        return FolderLayer::whereHas('userGroups', function ($query) use($userGroupId)
        {
            $query->whereIn('user_group_id', array(
                $userGroupId
            ));
        })->where($type, true)->first();
    }

    /**
     * Get User's layer by type
     *
     * @param String $type            
     * @param Int $userId            
     *
     * @return Illuminate\Database\Eloquent\Collection $layers
     */
    public function getUserLayerByType($type, $userId = null)
    {
        return FolderLayer::whereHas('map', function ($query) use($userId)
        {
            if (! empty($userId))
                $query->where('user_id', $userId);
        })->where($type, true)->where('is_invisible_layer', false)
            ->WhereNotNull('parent_folder')
            ->whereHas('folder', function($q){
                $q->where('is_recyclebin', '=',false );
            })
            ->orderBy('name', 'asc')
            ->get();

    }
}