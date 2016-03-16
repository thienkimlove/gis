<?php

namespace Gis\Models\Repositories;

/**
 * DefaultStandardCropNitoRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class DefaultStandardCropNitoRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class DefaultStandardCropNitoRepositoryEloquent extends GisRepository implements DefaultStandardCropNitoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\DefaultStandardCropNito';
    }
}