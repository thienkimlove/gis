<?php namespace Gis\Models\Services;

/**
 * Using for declaring methods list for database business layer.
 * Interface FertilizationPriceServiceInterface
 *
 * @package Gis\Models\Services
 */
interface FertilizationPriceServiceInterface extends BaseServiceInterface {
    function createFertilizationPrice($postData);
    function deleteFertilization(array $ids);
}