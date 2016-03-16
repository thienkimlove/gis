<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Entities\FertilizerEfficiencyOfCompost;

/**
 * Class FertilizerEfficiencyOfCompostRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class FertilizerEfficiencyOfCompostRepositoryEloquent extends BaseRepository implements FertilizerEfficiencyOfCompostRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilizerEfficiencyOfCompost';
    }

    /**
     * Find record by key
     * @param $postData
     * @return mixed
     */
    public function findRecordByKey($postData){
        $list = $this->model
            ->where('compost_type','=',$postData['select1'])
            ->where('application_time','=',$postData['select2'])
            ->get();
        foreach($list as $row){
            if(in_array($postData['crop'],explode(",",$row->crops_codes))){
                return $row;
            }
        }
    }
}