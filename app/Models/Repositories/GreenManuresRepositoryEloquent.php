<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Entities\GreenManures;

/**
 * Class GreenManuresRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class GreenManuresRepositoryEloquent extends BaseRepository implements GreenManuresRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\GreenManures';
    }

    /**
     * @param $postData
     * @return mixed
     */
    public function findRecordByKey($postData){
        $list = $this->model
            ->where('green_manure_type','=',$postData['select1'])
            ->where('cropping_type','=',$postData['select2'])
            ->where('exchangeable_potassium_content_type','=',$postData['select3'])
            ->get();
        foreach($list as $row){
            if(in_array($postData['crop'],explode(",",$row->crops_codes))){
                return $row;
            }
        }
    }
}