<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * User Entity
 * Class User
 *
 * @package Gis\Models\Entities
 */
class User extends Model
{

    public $timestamps = false;

    const STATUS_LOCK = 0;

    const STATUS_ACTIVE = 1;

    protected $fillable = [
        'username',
        'user_group_id',
        'password',
        'email',
        'code',
        'user_locked_flg',
        'login_failed_count',
        'is_agreed',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'last_logout_time',
        'token',
        'can_delete',
        'system_number'
    ];

    public function StateOfUser()
    {
        return $this->hasOne('Gis\Models\Entities\StateOfUser', 'user_code');
    }

    public function usergroup()
    {
        return $this->belongsTo('Gis\Models\Entities\Group', 'user_group_id');
    }

    public function user_with_group()
    {
        return $this->belongsTo('Gis\Models\Entities\Group', 'user_group_id')
            ->where('usergroups.auth_authorization', '=', false)
            ->where('usergroups.is_guest_group', '=', false);
    }

    public function fertility_maps()
    {
        return $this->hasMany('Gis\Models\Entities\FertilityMap', 'user_id');
    }

    public function fertilizer_maps()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerMap', 'user_id');
    }

    public function getArrayFertilityMaps()
    {
        $result[''] = trans('common.select_item_null');
        $fertilityMaps = $this->fertility_maps;
        
        if (! $fertilityMaps->isEmpty()) {
            foreach ($fertilityMaps as $map) {
                $layer = $map->folderlayer;
                $result[$map->id . '_' . $layer->id] = $layer->name;
            }
        }
        
        return $result;
    }
}