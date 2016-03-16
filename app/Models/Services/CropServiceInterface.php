<?php

namespace Gis\Models\Services;

/**
 * Using for declaring methods list for database business layer.
 * Interface CropServiceInterface
 *
 * @package Gis\Models\Services
 */
interface CropServiceInterface extends BaseServiceInterface{


	/**
	 * Get Array Crops.
	 *
	 * @param String $default
	 *
	 * @return array
	 */
	function getArrayCrops();
	function deleteCrops(array $ids);
}