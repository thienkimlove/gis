<?php namespace Gis\Models\Services;

/**
 * Using for declaring methods list for database business layer.
 * Interface OrganicMatterServiceInterface
 *
 * @package Gis\Models\Services
 */
interface OrganicMatterServiceInterface extends BaseServiceInterface {
    function getDataByProduct($postData);
    function getDataGreenManure($postData);
    public function getDataCompost($postData);
    function getDataFertilizerEfficiency($postData);
}