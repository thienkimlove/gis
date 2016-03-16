<?php

namespace Gis\Models\Repositories;

/**
 * StandardCropKaliRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class StandardCropKaliRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class StandardCropKaliRepositoryEloquent extends GisRepository implements StandardCropKaliRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\StandardCropKali';
    }
}