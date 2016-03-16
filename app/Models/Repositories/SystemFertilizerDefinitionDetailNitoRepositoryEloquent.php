<?php
namespace Gis\Models\Repositories;

/**
 * StandardCropNitoRepositoryEloquent provider functional access to database.It like same data provider layer.
 * Class StandardCropNitoRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class SystemFertilizerDefinitionDetailNitoRepositoryEloquent extends GisRepository implements SystemFertilizerDefinitionDetailNitoRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\SystemFertilizerDefinitionDetailNito';
    }

    /**
     * Get Collection System Fertilizer Definition Detail Nito by crops id
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