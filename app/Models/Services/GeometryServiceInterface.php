<?php

namespace Gis\Models\Services;

/**
 * The interface for the classes that implement the business logic regarding geometry data
 * Interface GeometryServiceInterface
 * @package Gis\Models\Services
 */
interface GeometryServiceInterface {

	/**
	 * @param $swLongitude
	 * @param $swLatitude
	 * @param $neLongitude
	 * @param $neLatitude
	 * @param $srid
	 * @return geometry data
	 */
	public function makeRectangleGeometry($swLongitude, $swLatitude, $neLongitude, $neLatitude, $srid);

	/**
	 * @param $geometry
	 * @return POLYGON((x x, y y, x y, x y, x y))
	 */
	public function extractGeometryToPolygon($geometry);

	/**
	 * @param $value
	 * @return boolean
	 */
	public function validateInputGeometry($value);

    /**
     * @param $swLongitude
     * @param $swLatitude
     * @param $neLongitude
     * @param $neLatitude
     * @return boolean
     */
	public function validateRectangleGeometry( $swLongitude, $swLatitude, $neLongitude, $neLatitude);

    /**
     * @param $srid
     * @return boolean
     */
    public  function validateSRID($srid);

    /**
     * @param $value
     * @return value
     */
    public function convertSTMakeEnvelopeToString($value);
    /**
     * @param $value
     * @return value
     */
    public function convertSTAsTextToString($value);

}