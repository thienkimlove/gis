<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Models\Entities\HojoByproduct;

/**
 * Class HojoByproductRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class HojoByproductRepositoryEloquent extends BaseRepository implements HojoByproductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\HojoByproduct';
    }

    /**
     * Get appropriate data
     * @param $postData
     * @return mixed
     */
    public function findRecordByKey($postData){
        if($postData['select2'] === ""){
            $list = $this->model
                ->where('organic_type','=',$postData['select1'])
                ->where('exchangeable_potassium_content_type','=',$postData['select3'])
                ->get();
        }else{
            $list = $this->model
                ->where('organic_type','=',$postData['select1'])
                ->where('processing_method_type','=',$postData['select2'])
                ->where('exchangeable_potassium_content_type','=',$postData['select3'])
                ->get();
        }

        foreach($list as $row){
            if(in_array($postData['crop'],explode(",",$row->crops_codes))){
                return $row;
            }
        }
    }
}