<?php
namespace Gis\Models\Services;


use Illuminate\Support\Facades\Facade;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */
class FertilityMapServiceFacade  extends  Facade{

  protected static function getFacadeAccessor() { return 'Gis\Models\Services\FertilityMapServiceInterface'; }

}