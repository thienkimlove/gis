<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 11:59 AM
 */
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class FolderLayer extends Model
{

    protected $table = 'folderlayers';

    public $timestamps = false;

    protected $fillable = [
        
        'parent_folder',
        'name',
        'order_number',
        'scale_type',
        'is_recyclebin',
        'is_terrain_folder',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'is_fertility_folder',
        'is_fertilizer_folder',
        'is_admin_folder',
        'is_invisible_layer',
        'old_parent_folder',
        'session_id'
    ];

    function folder()
    {
        return $this->hasone('Gis\Models\Entities\FolderLayer', 'id', 'parent_folder');
    }

    function layers()
    {
        return $this->hasMany('Gis\Models\Entities\FolderLayer', 'parent_folder');
    }

    function map()
    {
        return $this->hasOne('Gis\Models\Entities\FertilityMap', 'layer_id', 'id');
    }

    function fertilityMapExtend()
    {
        return $this->hasOne('Gis\Models\Entities\FertilityMapExtend', 'layer_id', 'id');
    }

    function fertilizerMap()
    {
        return $this->hasOne('Gis\Models\Entities\FertilizerMap', 'layer_id', 'id');
    }

    public function userGroups()
    {
        return $this->belongsToMany('Gis\Models\Entities\Group', 'folderusergroups', 'folder_id', 'user_group_id');
    }
}