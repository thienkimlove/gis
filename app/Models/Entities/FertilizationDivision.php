<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilization Devision Entity
 * Class FertilizationDivision
 *
 * @package Gis\Entities
 */
class FertilizationDivision extends Model
{

    protected $table = 'fertilization_divisions';

    public $timestamps = false;

    protected $fillable = array(
        'fertilization_classification_code',
        'fertilization_division',
        'n',
        'p',
        'k',
        'crops_code'
    );
}