<?php
namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Gis\Models\Entities\FolderLayer;

/**
 * Folder Repository interface, provider object to access data provider.
 * Interface FolderRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface FolderRepository extends RepositoryInterface
{

    /**
     * Define function Get All folders of application
     *
     * @return Illuminate\Database\Eloquent\Collection
     *
     */
    public function getAllFolders();
    /**
     *
     * @return list
     */
    /**
     * Define function Get user folders of application
     *
     * @return Illuminate\Database\Eloquent\Collection
     *
     */
    public function getUserFolders();

    /**
     *
     * @return list
     */
    public function getFolderNotBinAndNotTerrain();

    /**
     * Define function Check Create Data exists In DB
     *
     * @param String $field
     * @param String $value
     *
     * @return boolean
     */
    public function checkCreateDataExists($field, $value);

    /**
     * Define function Check Edit Data exists In DB
     *
     * @param String $field
     * @param String $value
     * @param Gis\Models\Entities\User $user
     *
     * @return boolean
     */
    public function checkEditDataExists($field, $value, FolderLayer $user);

    /**
     * Define function Get Max Folder ORder
     *
     * @return int
     */
    function getMaxFolderOrder();

    /**
     * Define function Find folder by Id
     *
     * @param int $id
     *
     * @return Gis\Models\Entities\FolderLayer $user
     */
    public function findById($id);

    /**
     * Define function Get List Folder Orders
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    function getListOrders();

    /**
     * Define method Get Folder By Type
     *
     * @return Gis\Models\Entities\FolderLayer
     */
    function getFolderTerrain();

    /**
     * Define method Check Folder exists from ids
     *
     * @param array() $ids
     *
     * @return int
     */
    public function countByIds($ids);

    /**
     * Define method Count layers with folder ids
     *
     * @param array() $ids
     *
     * @return int
     */
    public function countFolderContainLayerByIds($ids);

    /**
     * Define method get Folder from ids
     *
     * @param array() $ids
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getFolderByIds($ids);

    /**
     * Define method count layer terrain
     *
     * @return boolean
     */
    function countLayerTerrain();
}