<?php
/**
 * Created by PhpStorm.
 * User: haph1
 * Date: 8/4/2015
 * Time: 9:21 AM
 */
namespace Gis\Models\Repositories;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */


use Illuminate\Support\Facades\Facade;

class FertilizationPriceFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilizationPriceRepository'; }
}