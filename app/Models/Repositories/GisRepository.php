<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Gis Base Repository
 * Class GisRepository
 * @package Gis\Models\Repositories
 */

class GisRepository extends BaseRepository implements GisRepositoryInterface
{
    /**
     * Holder for model method in extended class.
     */
    public function model() {

    }
    /**
     * Search by conditions.
     * @param $conditions
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function whereConditions($conditions)
    {
        foreach ($conditions as $query) {
            list($field, $condition, $val) = $query;
            $this->model = $this->model->where($field,$condition,$val);
        }
        return $this->model;
    }

    /**
     * Delete multi records by ids.
     * @param array $ids
     * @return mixed
     */
    public function deleteMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Delete multi records by field.
     * @param array $ids
     * @return mixed
     */
    public function deleteByField(array $array, $field)
    {
        return $this->model->whereIn($field, $array)->delete();
    }

    /**
     * Get record by id
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Order by
     * @param $field
     * @param string $type
     * @return mixed
     */
    public function orderBy($field, $type = 'ASC')
    {    	
        return $this->model->orderBy($field, $type);
    }

    /**
     * return array [key => value, key1 => value]
     * @param $key
     * @param $value
     * @return mixed
     */
    public function lists($key, $value)
    {
        return $this->model->lists($key, $value);
    }

    public function selectModel()
    {
    	return $this->model;
    }
}