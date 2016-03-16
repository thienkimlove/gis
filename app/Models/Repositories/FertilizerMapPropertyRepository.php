<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface FertilizationPriceRepository
 * @package namespace Gis\Repositories;
 */
interface FertilizerMapPropertyRepository extends GisRepositoryInterface
{
    public function getCropID($fertilizer_map_id);
}
