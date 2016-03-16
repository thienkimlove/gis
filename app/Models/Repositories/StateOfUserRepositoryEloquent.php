<?php

namespace Gis\Models\Repositories;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class StateOfUserRepositoryEloquent extends GisRepository implements StateOfUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\StateOfUser';
    }
}