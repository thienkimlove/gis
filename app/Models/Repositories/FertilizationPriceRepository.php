<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface FertilizationPriceRepository
 * @package namespace Gis\Repositories;
 */
interface FertilizationPriceRepository extends GisRepositoryInterface
{
    //
    public function getAllDate($id);
    public function checkExistence($id);
}
