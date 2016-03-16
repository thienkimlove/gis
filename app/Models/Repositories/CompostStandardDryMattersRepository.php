<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CompostStandardDryMattersRepository
 * @package namespace Gis\Repositories;
 */
interface CompostStandardDryMattersRepository extends RepositoryInterface
{
    public function findRecordByKey($postData);
}
