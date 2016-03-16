<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * User Entity
 * Class User
 * @package Gis\Models\Entities
 */

class HelpLink extends Model  {

    protected $table = 'helplinks';
    public $timestamps = false;

	protected $fillable = [
        'address',
        'help',
        'popup_screen',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
    ];
}