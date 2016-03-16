<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FertilityMapSelection
 *
 * @package Gis\Models\Entities
 */
class FertilityMapSelection extends Model
{

    protected $table = 'fertility_map_selections';

    public $timestamps = false;

    protected $fillable = array(
        'id',
        'fertility_map_id',
        'crops_id',
        'user_id',
        'is_ready',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'fertilizer_map_id'
    );

    public function user()
    {
        return $this->belongsTo('Gis\Models\Entities\User', 'user_id');
    }

    public function fertilityMapSelectionInfo()
    {
        return $this->hasMany('Gis\Models\Entities\FertilityMapSelectionInfo', 'fertility_map_selection_id');
    }
}