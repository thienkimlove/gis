<?php

/**
 * Working with map and raster and layers.
 */

namespace Gis\Models;

use Illuminate\Support\Facades\DB;

class MapTools
{
    protected static $index_color = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
    protected static $is_fertilizer = false;
    const CTRL_METHOD_SORT = 6; //sort for sub barrel

    /**
     * Return extent string base on PostGIS function query.
     * @param $extent
     * @return mixed
     */
    private static function _clearExtent($extent)
    {
        $ext = str_replace('BOX(', '', $extent);
        $ext = str_replace(')', '', $ext);
        $ext = str_replace(',', ' ', $ext);

        return ($ext) ? $ext : '143.02370305716 43.1306112960333 143.025877679656 43.1320002093352';

    }

    /**
     * Get map content.
     * @param $attributes
     * @param $mainLayout
     * @return mixed|string
     */
    private static function _setMap($attributes, $mainLayout)
    {

        $content = file_get_contents($mainLayout);
        foreach ($attributes as $key => $val) {
            $content = preg_replace('/\$' . $key . '\$/', $val, $content);
        }
        return $content;
    }

    /**
     * Get layers content base on layer type.
     * @param $layers
     * @param $layout_path
     * @return string
     */
    private static function _setLayers($layers, $layout_path)
    {
        $str = '';
        foreach ($layers as $layer) {
            if ($layer['type'] == 'raster') {
                $content = file_get_contents($layout_path['raster']);
            } else {
                $content = file_get_contents($layout_path['postGIS']);
            }
            foreach ($layer['attributes'] as $key => $val) {
                $content = preg_replace('/\$' . $key . '\$/', $val, $content);
            }
            $str .= " \n " . $content;
        }

        return $str;
    }


    /**
     * Put map content to server.
     * @param $ars
     * @return mixed
     */
    private static function _postToServer($ars)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, (env('UPLOAD_PATH'))? env('UPLOAD_PATH') : "http://192.168.0.204/map/upload.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($ars));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }


    private static function _filename($userId)
    {
        return env('DB_HOST').'_'.env('DB_DATABASE').'_user' . $userId;
    }

    /**
     * Show map and properties - Later must move to services.
     * @param null $userId
     * @param null $layerId
     * @param bool|false $is_fertilizer
     * @param bool $export_pdf
     * @return array
     */
    public static function showMap($userId = null, $layerId = null, $is_fertilizer = false, $export_pdf = false)
    {
        //zoom is the default zoom mode when display map
        //its value will be changed when we show all fertility maps of user
        $mapProperties = [
            'file_name' => null,
            'central' => [],
            'layers' => null,
            'zoom' =>12
        ];

        if ($userId) {
            $layers = DB::table('fertility_maps')
                ->select(DB::raw('fertility_maps.layer_id as layer_id, fertility_maps.id as id'))
                ->join('folderlayers as l1', 'fertility_maps.layer_id', '=', 'l1.id')
                ->join('folderlayers as l2', 'l1.parent_folder', '=', 'l2.id')
                ->where(function ($query) {
                    $query->where('l2.is_admin_folder', false)
                            ->where('l2.is_fertility_folder', true);
                })
                ->where('l1.is_invisible_layer', false)
                ->where('fertility_maps.user_id', $userId)
                ->lists('layer_id', 'id');
            $temp = [];
            $fertilityIds = [];
            foreach ($layers as $mapId => $layerId) {
                $temp[] = 'layer' . $layerId;
                $fertilityIds[] = $mapId;
            }
            $layerExtent = self::getExtent('fertility_map_infos', 'fertility_id', $fertilityIds);
            $mapProperties['layers'] = implode(',', $temp);
            array_push($mapProperties['central'], $layerExtent->long);
            array_push($mapProperties['central'], $layerExtent->lat);
            $mapProperties['file_name'] = self::_filename($userId);
            $mapProperties['minZoom'] = 0;
            $mapProperties['zoom'] = 8;
            $mapProperties['bounding'] = explode(' ', self::_clearExtent($layerExtent->extent));
        } else {
            //check if this layer exist.
            $checkExist = DB::table('folderlayers')
                ->where('id', $layerId)
                ->whereNotNull('parent_folder')
                ->whereNotIn('name', ['25000', '50000'])
                ->first();
            if(!$checkExist) {
                return null;
            }
            $table_name = ($is_fertilizer) ? 'fertilizer_maps' : 'fertility_maps';
            $field_name = ($is_fertilizer) ? 'fertilizer_id' : 'fertility_id';
            $table_info_name = ($is_fertilizer) ? 'fertilizer_map_infos' : 'fertility_map_infos';
            $map = DB::table($table_name)->where('layer_id', $layerId)->first();
            if ($map) {
                $layerExtent = self::getExtent($table_info_name, $field_name, $map->id);
                array_push($mapProperties['central'], $layerExtent->long);
                array_push($mapProperties['central'], $layerExtent->lat);
                $mapProperties['file_name'] =  ($is_fertilizer) ? self::_filename($map->user_id. '_' . $map->id) : self::_filename($map->user_id);
                $mapProperties['layers'] = 'layer' . $layerId;

                //for export case, we must return more 2 params.
                if ($export_pdf) {
                    $mapProperties['fertilizer_map_name'] = DB::table('folderlayers')->find($layerId)->name;
                    $mapProperties['fertilizer_map_id'] = $map->id;
                }
            }
        }
        return $mapProperties;
    }

    /**
     * Get layer extent and central.
     * @param $table_name
     * @param $field_name
     * @param $mapId
     * @return mixed
     */

    private static function getExtent($table_name, $field_name, $mapId)
    {
        if (!is_array($mapId)) {
            $mapId = [$mapId];
        }

        return DB::table($table_name)
            ->select(DB::raw('ST_X(st_centroid(ST_ConvexHull(ST_Collect(geo)))) as long, ST_Y(ST_Centroid(ST_ConvexHull(ST_Collect(geo)))) as lat, ST_Extent(geo) as extent'))
            ->whereIn($field_name, $mapId)
            ->first();
    }


    /**
     * Generate Fertilizer layers and Fertility layers.
     * @param $map
     * @param bool|false $is_fertilizer
     * @return array
     */

    private static function _generateLayer($map, $is_fertilizer = false)
    {

        $table_name = ($is_fertilizer) ? 'fertilizer_map_infos' : 'fertility_map_infos';
        $field_name = ($is_fertilizer) ? 'fertilizer_id' : 'fertility_id';
        $extent = self::getExtent($table_name, $field_name, $map->id);
        $listLegend = null;
        if($is_fertilizer){
            $listLegend = self::makeLegendFertilizer($map->id);
        }
        $addition_query = '';
        return [
            'type' => 'postGIS',
            'attributes' => [
                'LAYER_NAME' => 'layer' . $map->layer_id,
                'LAYER_EXTENT' => self::_clearExtent($extent->extent),
                'LAYER_WMS_TITLE' => 'layer' . $map->layer_id,
                'LAYER_PROJECTION_UPPER' => 'EPSG:3857',
                'LAYER_PROJECTION_LOWER' => 'epsg:3857',
                'LAYER_CONNECTION_STRING' => 'host=' . env('DB_HOST') . ' dbname=' . env('DB_DATABASE')
                    . ' user=' . env('DB_USERNAME') . ' password=' . env('DB_PASSWORD'),
                'LAYER_CONNECTION_QUERY' => 'geo from (select *' . $addition_query . ' from ' . $table_name . ' where '
                    . $field_name . ' = ' . $map->id . ') as map using unique id using srid=3857',
                'CLASSITEM' => ($is_fertilizer) ? '' : 'CLASSITEM "nitrogen"' ,
                'LEGEND' => self::makeLegend($listLegend),
            ]
        ];
    }

    /**
     * Create or re-create map file for each user.
     * @param $userId
     */

    public static function mapFile($userId)
    {

        //get all layer_id for a current_user.

        $mapFilename = self::_filename($userId);
        $layerOptions = [];
        $fertilityMaps = DB::table('fertility_maps')
            ->where('user_id', $userId)
            ->get();

        if ($fertilityMaps) {
            foreach ($fertilityMaps as $map) {
                $layerOptions[] = self::_generateLayer($map);
            }
        }

     /*   $fertilizerMaps = DB::table('fertilizer_maps')
            ->where('user_id', $userId)
            ->get();

        if ($fertilizerMaps) {
            foreach ($fertilizerMaps as $map) {
                $layerOptions[] = self::_generateLayer($map, true);
            }
        }*/

        $layerString = self::_setLayers($layerOptions, array(
            'postGIS' => public_path('map/layout-postGIS.map')
        ));


        $mapOptions = [
            'MAP_NAME' => 'User ' . $userId,
            'MAP_SIZE' => '2000 2000',
            'MAP_IMAGE_PATH' => '/var/www/html/map/tmp/',
            'MAP_IMAGE_TMP_URL' => '/tmp/',
            'MAP_RESOURCE_URL' => env('MAPSERVER') . $mapFilename,
            'MAP_LAYERS' => $layerString,
        ];

        self::_postToServer(array(
            'file_name' => $mapFilename . '.map',
            'content' => self::_setMap($mapOptions, public_path('map/core-layout.map'))
        ));


    }
    static  function  makeLegendFertilizer($fertilizer_id){
        $colorLists = [];
        $tmp_sub_main = [];
        $index = 1;
        //variable to indicate that is one barrel fertilization
        $isOneBarrel = null;
        $isSortingBySub = null;
        $sortingSubBarrel = DB::table('fertilizer_map_properties')
            ->where('fertilizer_map_id', $fertilizer_id)
            ->get();

        $query = DB::table('fertilizer_map_infos')->where('fertilizer_id', $fertilizer_id);

        if($sortingSubBarrel[0]->fertilizing_machine_type =="1"){
            $isOneBarrel = true;
        }
        else{
            $isOneBarrel = false;
        }
        if($sortingSubBarrel[0]->control_methodology == self::CTRL_METHOD_SORT){
            $isSortingBySub = true;
        }
        else{
            $isSortingBySub = false;
        }
        if($isSortingBySub){
            $query->orderBy('sub_fertilizer', 'desc');
        }else{
            $query->orderBy('main_fertilizer', 'desc');
        }
        $fertilizerMapsResult = $query ->get();
        $colorLists[0] = array(
            'name' => "1",
            'express' => "",
            'color_code' => ""
        );
        foreach ($fertilizerMapsResult as $key) {
            $rgb = $key->r . ',' . $key->g . ',' . $key->b;
            if (!in_array($rgb, $tmp_sub_main)) {
                array_push($tmp_sub_main, $rgb);
                $r = $key->r ?  $key->r : 0;
                $g = $key->g ?  $key->g : 0;
                $b = $key->b ?  $key->b : 0;
                if(!$isOneBarrel){
                    $colorLists[$key->id] = array(
                        'name' => trans('common.common_legend_fertilizer_main').round($key->main_fertilizer)
                            .'、'.trans('common.common_legend_fertilizer_sub').round($key->sub_fertilizer),
                        'express' => "('[r]' == '$r' AND '[g]' == '$g' AND '[b]' == '$b' )",
                        'color_code' => $r . "\t" . $g .  "\t". $b
                    );
                }
                else{
                    $colorLists[$key->id] = array(
                        'name' => round($key->main_fertilizer),
                        'express' => "('[r]' == '$r' AND '[g]' == '$g' AND '[b]' == '$b' )",
                        'color_code' => $r . "\t" . $g .  "\t". $b
                    );
                }
                $index++;
            }
        }
        return $colorLists;

    }
    static  function  setRGBtoHexList($r,$g,$b){

        $r = self::convertRGBtoHex($r);
        $g = self::convertRGBtoHex($g);
        $b = self::convertRGBtoHex($b);
        return  $r . $g . $b;
    }
    static function convertRGBtoHex($value)
    {
        $r = dechex($value);
        return ($value < 16) ? $r = '0' . $r : $r;
    }


    /**
     * Generate legend content.
     * @return string
     */

    protected static function makeLegend($listLegend = null)
    {
        $content_legend = '';
        if (!empty($listLegend)) {

            foreach ($listLegend as $legend) {
                $content_legend .= self::setTemplateLegendFertilizer($legend['name'], $legend['express'], $legend['color_code']);
            }

        } else {
            foreach (self::setLegend() as $legend) {
                $content_legend .= self::setTemplateLegend($legend['name'], $legend['express'], $legend['color_code']);
            }
        }

        return $content_legend;
    }
    /**
     * legend template.
     * @param $name
     * @param $express
     * @param $color_code
     * @return string
     */
    public static function setTemplateLegendFertilizer($name, $express, $color_code)
    {
        if($name =="1")
        {
            return $tmp = "
             CLASS" . "\n
              \t NAME '" . trans('common.common_legend_fertilizer_title') . "'" . "\n
              \t EXPRESSION (true)
              \t STYLE\n
                \t SIZE 1\n
                  \tOUTLINECOLOR 255 255 255\n
                 \t COLOR 255 255 255\n
              \tEND\n
             \tEND";
        }
        else{
            return $tmp = "
             CLASS" . "\n
              \t NAME '" . $name . "'" . "\n
              \t EXPRESSION " . $express . "
              \t STYLE\n
                \t SIZE 1\n
                  \tOUTLINECOLOR 200 200 200\n
                 \t COLOR " . $color_code . "\n
              \tEND\n
             \tEND";
        }

    }

    /**
     * legend template.
     * @param $name
     * @param $express
     * @param $color_code
     * @return string
     */
    public static function setTemplateLegend($name, $express, $color_code)
    {
        if($name =="0")
        {
            return $tmp = "
             CLASS" . "\n
              \t NAME '肥沃度'" . "\n
              \t EXPRESSION (true)
              \t STYLE\n
                \t SIZE 1\n
                  \tOUTLINECOLOR 255 255 255\n
                 \t COLOR 255 255 255\n
              \tEND\n
             \tEND";
        }
        else{
            return $tmp = "
             CLASS" . "\n
              \t NAME '" . $name . "'" . "\n
              \t EXPRESSION '" . $express . "'
              \t STYLE\n
                \t SIZE 1\n
                  \tOUTLINECOLOR 200 200 200\n
                 \t COLOR " . $color_code . "\n
              \tEND\n
             \tEND";
        }
    }

    /**
     * Defined legend array.
     * @return array
     */

    public static function setLegend()
    {
        return array(
            1 => array(
                'name' => 0,
                'express' => 0,
                'color_code' => '255 255 255'
            ),
            2 => array(
                'name' => 1,
                'express' => 1,
                'color_code' => '0 0 255'
            ),
            3 => array(
                'name' => 2,
                'express' => 2,
                'color_code' => '0 102 204'
            ),
            4 => array(
                'name' => 3,
                'express' => 3,
                'color_code' => '0 153 204'
            ),
            5 => array(
                'name' => 4,
                'express' => 4,
                'color_code' => '0 204 255'
            ),
            6 => array(
                'name' => 5,
                'express' => 5,
                'color_code' => '0 255 0'
            ),
            7 => array(
                'name' => 6,
                'express' => 6,
                'color_code' => '255 255 0'
            ),
            8 => array(
                'name' => 7,
                'express' => 7,
                'color_code' => '255 204 0'
            ),
            9 => array(
                'name' => 8,
                'express' => 8,
                'color_code' => '255 153 51'
            ),
            10 => array(
                'name' => 9,
                'express' => 9,
                'color_code' => '255 102 0'
            ),
            11 => array(
                'name' => 10,
                'express' => 10,
                'color_code' => '255 0 0'
            ),
            12 => array(
                'name' => '11以上',
                'express' => 11,
                'color_code' => '153 0 0'
            ),
        );

    }


    public  static function makeFertilizerFile($fid,$userId){

        $mapFilename = self::_filename($userId) . '_' . $fid;
        $fertilizerMaps = DB::table('fertilizer_maps')
            ->where('id', $fid)
            ->where('user_id', $userId)
            ->get();

        if ($fertilizerMaps) {
            foreach ($fertilizerMaps as $map) {
                $layerOptions[] = self::_generateLayer($map, true);
            }
        }

        $layerString = self::_setLayers($layerOptions, array(
            'postGIS' => public_path('map/layout-postGIS.map')
        ));


        $mapOptions = [
            'MAP_NAME' => 'User ' . $userId . ' Fertilizer ' . $fid,
            'MAP_SIZE' => '2000 2000',
            'MAP_IMAGE_PATH' => '/var/www/html/map/tmp/',
            'MAP_IMAGE_TMP_URL' => '/tmp/',
            'MAP_RESOURCE_URL' => env('MAPSERVER') . $mapFilename,
            'MAP_LAYERS' => $layerString,
        ];

        self::_postToServer(array(
            'file_name' => $mapFilename . '.map',
            'content' => self::_setMap($mapOptions, public_path('map/core-layout.map'))
        ));
    }

}