<?php

namespace Gis\Models\Services;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */
use Illuminate\Support\Facades\Facade;

class HelpLinkServiceFacade extends Facade {
	protected static function getFacadeAccessor() {
		return 'Gis\Models\Services\HelpLinkServiceInterface';
	}
}