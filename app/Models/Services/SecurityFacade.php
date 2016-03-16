<?php

namespace Gis\Models\Services;

/**
 * Using Facade to avoid inject SecurityService in Constructor.
 */
use Illuminate\Support\Facades\Facade;

class SecurityFacade extends Facade {
	protected static function getFacadeAccessor() {
		return 'Gis\Models\Services\SecurityServiceInterface';
	}
}