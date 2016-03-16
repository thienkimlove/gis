<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Group Entity
 * Class Group
 * @package Gis\Entities
 */

class Group extends Model  {

    //Fields can be mass assignment.

    protected $table = 'usergroups';

    public $timestamps = false;

    protected $fillable = [
        'group_name',
        'description',
        'auth_change_username_password',
        'auth_user_registration',
        'auth_footer',
        'auth_help',
        'auth_folder_layer',
        'auth_fertilizer_price',
        'auth_purchasing_management',
        'auth_user_group',
        'auth_authorization',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'is_guest_group',
        'auth_user_fertilizer_definition',
        'can_delete'
    ];


    public function users()
    {
        return $this->hasMany('Gis\Models\Entities\User', 'user_group_id');
    }

    public function folders()
    {
        return $this->belongsToMany('Gis\Models\Entities\FolderLayer', 'folderusergroups','user_group_id','folder_id');
    }
}   