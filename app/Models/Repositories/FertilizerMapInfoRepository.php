<?php

namespace Gis\Models\Repositories;


/**
 * Crop Repository interface, provider object to access data provider.
 * Interface CropRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface FertilizerMapInfoRepository extends GisRepositoryInterface
{
    public function calculateBarrel($value, $meshSize);
}