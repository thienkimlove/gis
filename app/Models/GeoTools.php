<?php

namespace Gis\Models;


use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\FertilizerMap;
use Gis\Models\Services\FertilityMapServiceFacade;
use Illuminate\Support\Facades\DB;
use Gis\Helpers\DataHelper;

class GeoTools
{

    const SRID = '3857';
    const POLYGON_TYPE = 'Polygon';
    const BOX_TYPE = 'Box';
    protected $geojson_data;

    /**
     * get map information for export pdf
     * @param $id
     * @return mixed
     */
    public static function extractGeoForExport($data)
    {
        return DB::table('fertilizer_map_infos')
            ->select(DB::raw('ST_AsGeoJSON(geo) as json'))
            ->where('fertilizer_id', $data['fertilizer_id'])
            ->lists('json');
    }

    private static function _getMapSRID($id, $is_fertilizer = false)
    {
        if ($is_fertilizer) {
            $fertilityMap = DB::table('fertilizer_maps')
                ->leftJoin('fertility_maps', 'fertilizer_maps.fertility_map_id', '=', 'fertility_maps.id')
                ->where('fertilizer_maps.id', $id)
                ->select('fertility_maps.coordinates_system_number', 'fertility_maps.id')
                ->first();
        } else {
            $fertilityMap = DB::table('fertility_maps')->find($id);
        }
        if ($fertilityMap) {
            return FertilityMapServiceFacade::reflectSRID($fertilityMap->coordinates_system_number);
        }
    }


    public static function displayFinal($data)
    {
        $obj = [];
       if (!empty($data['items'])) {
           foreach ($data['items'] as $item) {
               $result = DB::table('fertilizer_map_infos')->select(DB::raw('ST_displayFinal(geo, ST_SetSRID(ST_GeomFromGeoJSON(\''.json_encode($item['geo']).'\'), '.self::SRID.')) as json, ST_AsGeoJson(geo) as square, ST_AsGeoJson(ST_SetSRID(ST_GeomFromGeoJSON(\''.json_encode($item['geo']).'\'), '.self::SRID.')) as line'))
                   ->where('id', $item['id'])
                   ->first();
               $obj['points'][] = $result->json;
               $obj['squares'][] = $result->square;
               $obj['lines'][] = $result->line;
           }
       }
        return $obj;
    }

    /**
     * Calculate to draw prediction image
     * @param $data
     * @return array
     */
    public static function extractCoordinate($data){

        $json = [
            'type' =>  (!empty($data['vector']) && $data['vector'] == 'true') ? 'LineString' : 'Polygon',
            'coordinates' => $data['polygon']
        ];
        $selection = [];

        $table_name = 'fertilizer_map_infos';
        $table_field = 'fertilizer_id';
        if ($data['is_fertilizer'] != "false") {
            $tmpMap = FertilizerMap::where('layer_id', $data['layer_id'])->first();
            if(empty($tmpMap)){
                return false;
            }
        } else {
            $table_name = 'fertility_map_infos';
            $table_field = 'fertility_id';
            $tmpMap =  FertilityMap::where('layer_id', $data['layer_id'])->first();
            if(empty($tmpMap)){
                return false;
            }
            if (!empty($data['mode_selection_info_ids'])) {
                $selection = $data['mode_selection_info_ids'];
            }
        }
        $id =  $tmpMap->id;
        if  (!empty($data['vector']) && $data['vector'] == 'true') {
            return self::_getDataForPrediction($data,$table_name,$table_field,$json,$id);
        } else {
            $query = DB::table($table_name)
                ->select(DB::raw('ST_AsGeoJSON(geo) as json'),'id')
                ->where($table_field, $id)
                ->where(DB::raw('ST_Intersects(ST_SetSRID(ST_GeomFromGeoJSON(\''.json_encode($json).'\'), '.self::SRID.'),
            geo)'), true);
            if ($selection) {
                $query->whereIn('id', $selection);
            }
            return $query->lists('json','id');
        }
    }

    /**
     * Convert Polygon selection area to geometry
     * @param $polygon
     * @return mixed
     */
    public static function extractGeoFromPolygon($polygon){
        $queryTemplate = "SELECT ST_GeomFromText('POLYGON((%s))',%s);";
        $rawQuery = sprintf($queryTemplate,$polygon,self::SRID);
        return DB::select($rawQuery);
    }

    /**
     * Get data for prediction shortage of fertilization
     * @param $data
     * @param $table_name
     * @param $table_field
     * @param $json
     * @param $id
     * @return array
     */
    private static function _getDataForPrediction($data,$table_name,$table_field,$json,$id){
        $parallel_distance = 10;
        $field_width = 500;
        if(!empty($data["fertilizer_width"])) {
            $parallel_distance = $data["fertilizer_width"];
        }

        if(!empty($data["field_width"])) {
            $field_width = $data["field_width"];
        }
        $result = [
            'lines' => [],
            'vector' => true,
            'fertilizer_map_id' => $id
        ];

        $query = DB::table($table_name)
            ->select(DB::raw('ST_AsGeoJSON(ST_MakeParallel(ST_BuildArea(ST_Collect(geo)), ST_SetSRID(ST_GeomFromGeoJSON(\''.json_encode($json).'\'), '.self::SRID.'), '.$parallel_distance.', '.$field_width.', '.$id.', '.self::_getMapSRID($id, true).')) as json'))
            ->where($table_field, $id);

        $result['lines'] = $query->lists('json');
        return $result;
    }
    /**
     * Predict shortage location of fertilization
     * @param $data
     * @return mixed
     */

    public static function storeGuestMap($data)
    {
        $rows = DB::table('fertilizer_map_infos')
            ->select(DB::raw('to_json(ST_StoreGuestMap(ST_BuildArea(ST_Collect(geo)), ST_GeomFromGeoJSON(\''.$data['geoJson'].'\') )) as json'))
            ->where('fertilizer_id', $data['fertilizer_map_id'])
            ->get();
        $temp = array();
        foreach ($rows as $index => $data) {
            array_push($temp, json_decode($data->json, true));
        }
        return $temp;
    }

    /*
     * using three GEO json : point, line, polygons
     * to generate a list of direction linestring
     */

    public static function generateGuessDirection($data)
    {
        $rows = DB::table('fertilizer_map_infos')
                    ->select(DB::raw('to_json(ST_generateDirection(ST_GeomFromGeoJSON(\''.json_encode($data['point']).'\'), ST_GeomFromGeoJSON(\''.json_encode($data['line']).'\'), ST_GeomFromGeoJSON(\''.json_encode($data['polygons']).'\'), ST_BuildArea(ST_Collect(geo)),'.$data['fertilizer_map_id'].', '.self::_getMapSRID($data['fertilizer_map_id'], true).')) as json'))
            ->where('fertilizer_id', $data['fertilizer_map_id'])
            ->get();
        $temp = array();
        foreach ($rows as $index => $data) {
            array_push($temp, json_decode($data->json, true));
        }
        return $temp;
    }

    /**
     * Get the previous selection map information
     * @param $ids
     * @return mixed
     */
    public static function extractSelectionCoordinate($ids)
    {
        $listMapInfoIds = DB::table('fertility_map_selection_infos')
            ->whereIn('fertility_map_selection_id', $ids)
            ->lists('map_info_id');

        $result = DB::table('fertility_map_infos')
            ->select(DB::raw('ST_AsGeoJSON(geo) as json'),'id')
            ->whereIn('id', $listMapInfoIds)
            ->lists('json','id');
        return $result;
    }

    /**
     * Using custom Postgres SQL function ST_MakeGrid (Source : app/Models/sql/new.sql)
     * return list of new meshes geo and contribute percent of old meshes.
     * @param $fertilityMapId
     * @param $newMeshSize
     * @param $mapInfosIdList - array of ids
     * @return array
     */

    public static function reCreateUserChosenWithMeshSize($fertilityMapId, $newMeshSize, $mapInfosIdList)
    {
        return DB::table('fertility_map_infos')
            ->select(DB::raw('to_json(st_makegrid(ST_BuildArea(ST_Collect(geo)), '.$fertilityMapId.', '.$newMeshSize.', '.self::_getMapSRID($fertilityMapId).",'{".implode(',',$mapInfosIdList)."}')) as result"))
            ->whereIn('id', $mapInfosIdList)
            ->lists('result');

    }

}