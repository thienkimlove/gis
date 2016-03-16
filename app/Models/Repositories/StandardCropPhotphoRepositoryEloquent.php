<?php

namespace Gis\Models\Repositories;

/**
 * StandardCropPhotphoRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class StandardCropPhotphoRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class StandardCropPhotphoRepositoryEloquent extends GisRepository implements StandardCropPhotphoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\StandardCropPhotpho';
    }
}