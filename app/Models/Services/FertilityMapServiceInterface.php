<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 1:21 PM
 */

namespace Gis\Models\Services;

/**
 * The interface for the classes that implements the business to process data regarding Fertility map
 * Interface FertilityMapServiceInterface
 * @package Gis\Models\Services
 */
interface FertilityMapServiceInterface
{

	/**
	 * @param $file
	 * @param $userId
	 * @param $fileName
	 * @return array
	 */
	public function importLayer($file, $userId, $fileName, $folder_id);

	/**
	 * @param $userId
	 * @param $fileName
	 * @return boolean
	 */
	public function createFertilityMap( $userId, $fileName, $folder_id);


	/**
	 * define function Filter users with conditions
	 *
	 * @param array() $postData
	 * @param array() $paggingRequest
	 *
	 */
	public function filterFertilityMap($postData, $pagingRequest);

	/**
	 * @param $ids
	 * @return boolean
	 */
	public function deleteFertilityMap($ids);

	/**
	 * Validate Zone Classification Code
	 * @param $row
	 * @return boolean
	 */
	public function validateCoordinateSystemNumber($row);

	/**
	 * @param $val
	 * @return boolean
	 */
	public function validateInteger($val);

	/**
	 * @param $val
	 * @return boolean
	 */
	public function validateOtherValueOfCode($val);

	/**
	 * @param $rows
	 * @return boolean
	 */
	public function  countColumn($row ,$rows);

	/**
	 * @param $row
	 * @param $rows
	 * @return boolean
	 */
	public function validateRowWithCode($row, $rows);

    /**
     * @param $rows
     * @param $srid
     * @return geometry
     */
    public function makeGeo($rows, $srid);

	/**
	 * define function Get All admin's map
	 *
	 * @return array() Gis\Models\Entities\FertilityMap
	 */
	public function getAllAdminMaps();

    /**
     * @param $srid
     * @return SRID
     */
    public function reflectSRID($srid);

	/**
	 * @param $mapId
	 * @return mixed
	 */
	public function showMapList($mapId);

}