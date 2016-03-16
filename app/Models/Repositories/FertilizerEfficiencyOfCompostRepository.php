<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface FertilizerEfficiencyOfCompostRepository
 * @package namespace Gis\Repositories;
 */
interface FertilizerEfficiencyOfCompostRepository extends RepositoryInterface
{
    public function findRecordByKey($postData);
}
