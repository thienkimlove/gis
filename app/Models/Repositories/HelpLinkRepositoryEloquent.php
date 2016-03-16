<?php
namespace Gis\Models\Repositories;

class HelpLinkRepositoryEloquent extends GisRepository implements HelpLinkRepository
{
	
	public function model() {
		return 'Gis\Models\Entities\HelpLink';
	}
}