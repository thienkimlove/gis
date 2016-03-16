<?php

namespace Gis\Models\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Gis base Repository Interface
 * Interface GisRepositoryInterface
 * @package Gis\Models\Repositories
 */

interface GisRepositoryInterface extends RepositoryInterface
{
    public function deleteMany(array $id);
    public function getById($id);
    public function orderBy($field, $type);
    public function lists($field, $type);
    public function whereConditions($conditions);
}