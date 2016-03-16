<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface GreenManuresRepository
 * @package namespace Gis\Repositories;
 */
interface GreenManuresRepository extends RepositoryInterface
{
    public function findRecordByKey($postData);
}
