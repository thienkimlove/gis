<?php

namespace Gis\Models\Repositories;


/**
 * Crop Repository interface, provider object to access data provider.
 * Interface CropRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface FertilizerMapPaymentRepository extends GisRepositoryInterface
{
    public function getListOfPaymentForWithTheSameCropsAndFertilizer($layerId);
}