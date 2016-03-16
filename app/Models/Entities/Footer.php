<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * User Entity
 * Class User
 * @package Gis\Models\Entities
 */

class Footer extends Model  {
	
	public $timestamps = false;
	protected $fillable = [
        'content',
        'version',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
    ];
}