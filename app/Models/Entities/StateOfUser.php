<?php 
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StateOfUser Entity
 * Class StateOfUser
 * @package Gis\Entities
 */

class StateOfUser extends Model  {

    //Fields can be mass assignment.

    protected $table = 'state_of_users';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_code',
        'is_invisible_scalebar',
        'is_invisible_legend',
        'is_invisible_zoom_toolbar',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'last_active_layer_id'
    ];
    public function user()
        {
            return $this->belongsTo('Gis\Models\Entities\User', 'user_code');
        }
    

}