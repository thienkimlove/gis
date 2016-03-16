<?php

namespace Gis\Models\Services;


use Illuminate\Support\Facades\DB;

/**
 * Service to perform business logic regarding gis map
 * Class GeometryService
 * @package Gis\Models\Services
 */
class GeometryService extends  BaseService implements GeometryServiceInterface{

    public $defaultSRID = 3857;

	/**
     * Convert the long&lat to geometry data
	 * @param $swLongitude
	 * @param $swLatitude
	 * @param $neLongitude
	 * @param $neLatitude
	 * @param $srid
	 * @return geometry data
	 */
	public function makeRectangleGeometry( $swLongitude, $swLatitude, $neLongitude, $neLatitude, $srid ) {

        if( $this->validateRectangleGeometry( $swLongitude, $swLatitude, $neLongitude, $neLatitude ) && $this->validateSRID( $srid )){
            return DB::raw("ST_Transform(ST_MakeEnvelope($swLatitude, $swLongitude,$neLatitude, $neLongitude, $srid), $this->defaultSRID)");
        }
        return FALSE;
	}

    /**
     * Convert the coordinate to appropriate data
     * @param $value
     * @return value
     */
    public function convertSTMakeEnvelopeToString( $value )
    {
        return $value[0]->st_makeenvelope;
    }


    /**
     * Extract the geometry data to polygon
	 * @param $geometry
	 * @return POLYGON((x x, y y, x y, x y, x y))
	 */
	public function extractGeometryToPolygon( $geometry ) {
		$result =  DB::select("SELECT ST_AsText('$geometry')");
		return $this->convertSTAsTextToString($result);
	}
    /**
     * Convert the STA to string data
     * @param $value
     * @return value
     */
    public function convertSTAsTextToString( $value )
    {
        return $value[0]->st_astext;
    }

    /**
     * Validate geometry data
     * @param $value
     * @return boolean
     */
    public function validateInputGeometry( $value )
    {
        return ctype_alnum((string) $value);
    }

    /**
     * Validate the rectangle geometry
     * @param $swLongitude
     * @param $swLatitude
     * @param $neLongitude
     * @param $neLatitude
     * @return boolean
     */
    public function validateRectangleGeometry(  $swLongitude, $swLatitude, $neLongitude, $neLatitude )
    {
        return is_numeric( $swLongitude ) && is_numeric( $swLatitude ) && is_numeric( $neLongitude ) && is_numeric( $neLatitude);
    }

    /**
     * Validate SRD data
     * @param $srid
     * @return boolean
     */
    public function validateSRID( $srid )
    {
       return (bool) preg_match('/^[\-+]?[0-9]+$/', $srid);
    }


}