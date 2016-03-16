<?php
namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

class FertilityMapSelectionInfoRepositoryEloquent extends BaseRepository implements FertilityMapSelectionInfoRepository
{

    public $timestamps = false;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilityMapSelectionInfo';
    }

    /**
     * Create new fertilitymap selection info with infomation
     *
     * @param array() $attributes            
     *
     * @return boolean
     */
    public function createFertilityMapselectionInfo($attributes)
    {
        return $this->model->insert($attributes);
    }
}