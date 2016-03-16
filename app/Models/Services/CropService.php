<?php
namespace Gis\Models\Services;

use Gis\Models\Repositories\CropFacade;
use Gis\Exceptions\GisException;
use Gis\Models\SystemCode;

/**
 * Service to perform business regarding crops
 * Methods to work with repositories.
 * Class FertilizerService
 *
 * @package Gis\Models\Services
 */
class CropService extends BaseService implements CropServiceInterface
{

    /**
     * Get list of crops from the database
     * @return mixed
     */
    function getArrayCrops()
    {
        $array[''] = trans('common.select_item_null');
        
        $crops = CropFacade::orderBy('order_number','ASC')->get();
        if ($crops != null) {
            foreach ($crops as $crop) {
                $array[$crop->id] = $crop->crops_name;
            }
        }
        return $array;
    }

    /**
     * Delete crops by list of crops ids
     * @param array $ids the list of crops id
     */
    function deleteCrops(array $ids)
    {
        CropFacade::deleteMany($ids);
    }

    /**
     * Find crops by crops id
     *
     * @param int $id the crops id
     * @return crops information
     */
    function findById($id)
    {
        $crop = CropFacade::findByField('id', $id)->first();
        
        if (empty($crop))
            throw new GisException(trans('common.standardcrop_crop_not_exists'), SystemCode::NOT_FOUND);
        
        return $crop;
    }
}

