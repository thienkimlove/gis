<?php
/**
 * Created by PhpStorm.
 * User: haph1
 * Date: 8/19/2015
 * Time: 1:55 PM
 */
namespace Gis\Models\Repositories;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */


use Illuminate\Support\Facades\Facade;

class FertilizerMapPropertyFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilizerMapPropertyRepository'; }
}