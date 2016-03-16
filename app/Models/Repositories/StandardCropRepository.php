<?php

namespace Gis\Models\Repositories;


/**
 * StandardCrop Repository interface, provider object to access data provider.
 * Interface StandardCropRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface StandardCropRepository extends GisRepositoryInterface
{
	public function getByCropId($standardId, $cropId);
}