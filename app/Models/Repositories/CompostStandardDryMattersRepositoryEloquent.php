<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Entities\CompostStandardDryMatters;

/**
 * Class CompostStandardDryMattersRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class CompostStandardDryMattersRepositoryEloquent extends BaseRepository implements CompostStandardDryMattersRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\CompostStandardDryMatters';
    }

    /**
     * find the record by key
     * @param $postData
     * @return mixed
     */
    public function findRecordByKey($postData){
        return $this->model
            ->where('compost_type','=',$postData['select1'])
            ->first();
    }
}