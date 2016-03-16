<?php

namespace Gis\Models\Repositories;

/**
 * DefaultStandardCropPhotphoRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class DefaultStandardCropPhotphoRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class DefaultStandardCropPhotphoRepositoryEloquent extends GisRepository implements DefaultStandardCropPhotphoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\DefaultStandardCropPhotpho';
    }
}