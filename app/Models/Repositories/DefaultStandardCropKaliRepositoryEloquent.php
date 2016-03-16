<?php

namespace Gis\Models\Repositories;

/**
 * DefaultStandardCropKaliRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class DefaultStandardCropKaliRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class DefaultStandardCropKaliRepositoryEloquent extends GisRepository implements DefaultStandardCropKaliRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\DefaultStandardCropKali';
    }
}