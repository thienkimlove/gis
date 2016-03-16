<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardUser Entity
 * Class StandardUser
 * @package Gis\Entities
 */

class StandardUser extends Model  {

    //Fields can be mass assignment.

    protected $table = 'fertilizer_standard_user_relations';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_standard_definition_id',
        'user_code',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];
    

}