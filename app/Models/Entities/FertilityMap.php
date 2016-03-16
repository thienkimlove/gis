<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 11:01 AM
 */
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FertilityMap
 *
 * @package Gis\Models\Entities
 */
class FertilityMap extends Model
{

    protected $table = 'fertility_maps';

    public $timestamps = false;

    protected $fillable = array(
        'user_id',
        'coordinates_system_number',
        'layer_id',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user'
    );

    public function user()
    {
        return $this->belongsTo('Gis\Models\Entities\User', 'user_id');
    }

    public function folderLayer()
    {
        return $this->belongsTo('Gis\Models\Entities\FolderLayer', 'layer_id');
    }

    public function mapinfo()
    {
        return $this->hasMany('Gis\Models\Entities\MapInfo', 'fertility_id');
    }

    function fertilizerMap()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerMap', 'fertility_map_id');
    }
    function fertilityMapSelection()
    {
        return $this->hasMany('Gis\Models\Entities\FertilityMapSelection', 'fertility_map_id');
    }
}