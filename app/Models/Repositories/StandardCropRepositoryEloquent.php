<?php
namespace Gis\Models\Repositories;

/**
 * StandardCropr repository provider functional access to database.It like same data provider layer.
 * Class StandardCropRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class StandardCropRepositoryEloquent extends GisRepository implements StandardCropRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\StandardCrop';
    }

    public function getByCropId($standardId, $cropId)
    {
        return $this->model->where('fertilizer_standard_definition_id', '=', $standardId)
            ->where('crops_id', '=', $cropId)
            ->where('not_available',false)
            ->first(); // ->get();
    }
    public function getByCropIdAvaiAndNot($standardId, $cropId)
    {
        return $this->model->where('fertilizer_standard_definition_id', '=', $standardId)
            ->where('crops_id', '=', $cropId)
            ->first(); // ->get();
    }

    /**
     * Get Collection user fertilizer definition details by crops id
     *
     * @param int $cropsId            
     *
     * @return Illuminate\Database\Eloquent\Collection $collection
     */
    function getListStandardCropByCropId($cropsId)
    {
        return $this->model->select('fertilizer_standard_definition_id')
            ->where('crops_id', '=', $cropsId)
            ->groupBy('fertilizer_standard_definition_id')
            ->get();
    }
}