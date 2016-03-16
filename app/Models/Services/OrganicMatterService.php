<?php
namespace Gis\Models\Services;

use Gis\Models\Repositories\HojoByproductFacade;
use Gis\Models\Repositories\GreenManuresFacade;
use Gis\Models\Repositories\FertilizerEfficiencyOfCompostFacade;
use Gis\Models\Repositories\CompostStandardDryMattersFacade;
use Gis\Exceptions\GisException;
use Illuminate\Support\Facades\File;

/**
 * Methods to work with repositories.
 * Class OrganicMatterService
 *
 * @package Gis\Models\Services
 */
class OrganicMatterService extends BaseService implements OrganicMatterServiceInterface
{
    /**
     * Retrieve organic matter by product
     * @param $postData
     * @return mixed
     */
    function getDataByProduct($postData){

        $data = HojoByproductFacade::findRecordByKey($postData);
        return $data;
    }

    /**
     * Retrieve organic matter by green manure
     * @param $postData
     * @return mixed
     */
    function getDataGreenManure($postData){
        $data = GreenManuresFacade::findRecordByKey($postData);
        return $data;
    }

    /**
     * Retrieve organic matter by compost
     * @param $postData
     * @return mixed
     */
    function getDataCompost($postData){
        $data = CompostStandardDryMattersFacade::findRecordByKey($postData);
        return $data;
    }

    /**
     * Retrieve organic matter by fertilizer efficiency
     * @param $postData
     * @return mixed
     */
    function getDataFertilizerEfficiency($postData){
        $data = FertilizerEfficiencyOfCompostFacade::findRecordByKey($postData);
        return $data;
    }
}