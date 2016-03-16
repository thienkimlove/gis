<?php

namespace Gis\Models\Repositories;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerUnitPriceRepositoryEloquent extends GisRepository implements FertilizerUnitPriceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\FertilizerUnitPrice';
    }

    /**
     * @param $currentDate
     * @return mixed
     */
    public function getUnitPrice($currentDate)
    {
        return $this->model
            ->orWhere(function ($query) use($currentDate)  {
                $query->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate);
            } )
            ->orWhere(function ($query) use($currentDate)  {
                $query->where('start_date', '<=', $currentDate)
                    ->whereNull('end_date');
            } )
            ->value('price');
    }
}