<?php
namespace Gis\Models\Repositories;

class FooterRepositoryEloquent extends GisRepository implements FooterRepository{

    /**
     * Specific model name
     * @return string
     */
    public function model() {
		return 'Gis\Models\Entities\Footer';
	}


}