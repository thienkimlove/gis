<?php

namespace Gis\Models\Repositories;

/**
 * StandardCropNitoRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class StandardCropNitoRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class SystemFertilizerDefinitionDetailPhotphoRepositoryEloquent extends GisRepository implements SystemFertilizerDefinitionDetailPhotphoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\SystemFertilizerDefinitionDetailPhotpho';
    }

}