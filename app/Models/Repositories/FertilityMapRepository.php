<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 1:11 PM
 */
namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface FertilityMapRepository extends RepositoryInterface
{

    /**
     * get list user with fol
     *
     * @return array
     */
    public function getFertilityMapWithNoFolderLayer();

    /**
     * Define method Find folder by Id
     *
     * @param int $id            
     *
     * @return Gis\Models\Entities\FertilityMap $fertilityMap
     */
    public function findById($id);

    public function getListNitrogenByFertilityMapId($fertilityMapId);


}