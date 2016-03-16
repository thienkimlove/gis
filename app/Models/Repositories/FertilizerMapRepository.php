<?php

namespace Gis\Models\Repositories;


/**
 * Crop Repository interface, provider object to access data provider.
 * Interface CropRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface FertilizerMapRepository extends GisRepositoryInterface
{
    public function getFertilizerMapId($layer_id);
    public function getFertilityMapId($layer_id);
}