<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface HojoByproductRepository
 * @package namespace Gis\Repositories;
 */
interface HojoByproductRepository extends RepositoryInterface
{
    public function findRecordByKey($postData);
}
