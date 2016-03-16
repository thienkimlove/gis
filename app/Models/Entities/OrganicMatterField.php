<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * OrganicMatterField Entity
 * Class Fertilizer
 *
 * @package Gis\Entities
 */
class OrganicMatterField extends Model
{

    protected $table = 'organic_matter_fields';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_map_id',
        'organic_matter_field_type',
        'n',
        'p',
        'k',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];
}





