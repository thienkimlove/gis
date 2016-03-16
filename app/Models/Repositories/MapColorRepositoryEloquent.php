<?php
namespace Gis\Models\Repositories;

use Gis\Models\Entities\MapColor;

/**
 * MapColor repository provider functional access to database.It like same data provider layer.
 * Class MapColorRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class MapColorRepositoryEloquent extends GisRepository implements MapColorRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\MapColor';
    }

    /**
     * Find all Map colors by number of pattern
     *
     * @param unknown $numbOfPettern            
     *
     * @return Illuminate\Database\Eloquent\Collection $collection $mapColors
     */
    public function findByNumbOfPettern($numbOfPettern)
    {
        return MapColor::Select(array(
            'r',
            'g',
            'b'
        ))->where('fertilization_number_of_patterns', $numbOfPettern)
            ->orderBy('fertilization_pattern', 'asc')
            ->get();
    }
}