<?php
/**
 * Created by PhpStorm.
 * User: haph1
 * Date: 8/11/2015
 * Time: 9:52 AM
 */
namespace Gis\Models\Repositories;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */


use Illuminate\Support\Facades\Facade;

class FertilizerEfficiencyOfCompostFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilizerEfficiencyOfCompostRepository'; }
}