<?php

namespace Gis\Models\Repositories;


/**
 * StandardUser Repository interface, provider object to access data provider.
 * Interface StandardUserRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface StandardUserRepository extends GisRepositoryInterface
{

	public function deleteItems($ids);
}