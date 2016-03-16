<?php

namespace Gis\Models\Repositories;

use Illuminate\Support\Facades\Facade;

class FertilityMapSelectionFacade  extends  Facade{

  protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilityMapSelectionRepository'; }

}