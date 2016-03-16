<?php

namespace Gis\Models\Services;
use Gis\Models\Entities\FertilizerMapInfo;

/**
 * Using for declaring methods list for database business layer.
 * Interface FertilizerServiceInterface
 *
 * @package Gis\Models\Services
 */
interface FertilizerServiceInterface extends BaseServiceInterface{
	function getById($id);
	function getStandardCropById($id);
	function getFertilizers($postDat, $paggingRequest);
	function getSpecifyUsers($postDat, $paggingRequest);
	public function getStandardCropDetails($standardCropId, $paggingRequest);
	function deleteFertilizers(array $ids);
	function saveFertilizer(array $postData);
	function saveStandardUser(array $postData);
	function copyFertilizer(array $postData);
	function getDefaultColorsList($fertilizerDetails);
	function getColorForFertilizerMap($layerId,$mapInfoId = []);
	function convertRGBtoHex($value);
    public function getBuyFertilizerData($layer_id);
    public function getSystemStandardCropDetails($fertilizerStandardId,$standardCropId, $paggingRequest);
	public function clearSystemStandardCropDetails($fertilizerStandardId,$standardCropId, $paggingRequest);
	public  function  extractColorFertilizerMap($fertilizerIterator);
	public  function  setRGBtoHexList($r,$g,$b);
	public  function  getColorOfFertilizerMapInfo($fertilizerId);
    public function fertilizerPropetiesData($layerid);
    public function getIsBinParentFertilizerMap(FertilizerMapInfo $item);
	public function  getControlMethodology($fertilizerId);
//	public function activePayment($request);
    function getArrayNormalFertilizer();
	public function validColorListDetails($postData);
	public function calculatePrice($unit_price, $area);
	public function getColorCurrentFertilizerMap($layerId);
	public function  mergeColorCurrentFertilizerMap($postData);
	public function  getFertilizerMapInfoById($id);

}