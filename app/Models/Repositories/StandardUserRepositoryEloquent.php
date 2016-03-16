<?php

namespace Gis\Models\Repositories;

/**
 * StandardUser repository provider functional access to database.It like same data provider layer.
 * Class StandardUserRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class StandardUserRepositoryEloquent extends GisRepository implements StandardUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\StandardUser';
    }

    public function deleteItems($ids)
    {
    	return $this->model->whereIn('id', $ids)->delete();
    }   
    
}