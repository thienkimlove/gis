<?php

namespace Gis\Models\Repositories;


/**
 * Crop Repository interface, provider object to access data provider.
 * Interface CropRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface CropRepository extends GisRepositoryInterface
{
    public function getCropName($cropID);
}