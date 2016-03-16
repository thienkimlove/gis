<?php
namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Fertilizer Repository interface, provider object to access data provider.
 * Interface FertilizerRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface FertilizerRepository extends RepositoryInterface
{

    public function getFertilizers($limit, $orderBy, $orderType, $userCode, $isAdmin);

    public function deleteMany(array $ids);

    public function getLimitFertilizers($keyword = null);

    /**
     * Define method Insert fertilizer map data to db
     *
     * @param array() $attributes            
     * @return Gis\Models\Entities\FertilizerMap $fertilizerMap
     */
    public function createFertilizerMap($attributes);

    /**
     * Insert fertilizer property data to db
     *
     * @param array() $attributes            
     * @return Gis\Models\Entities\FertilizerMapProperty $fertilizerproperty
     */
    public function createFertilizerProperty($attributes);

    public function getFertilizersByCropAndUserCode($cropId);
}