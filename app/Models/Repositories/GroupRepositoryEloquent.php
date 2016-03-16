<?php namespace Gis\Models\Repositories;

/**
 * Class GroupRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class GroupRepositoryEloquent extends GisRepository implements GroupRepository {

    public function model()
    {
        return 'Gis\Models\Entities\Group';
    }

    /*
     * check exist guest user
     */
    public function checkExistGuestUser(){
        $check = $this->model->where("is_guest_group", true)->first();
        if ($check)
            return true;
        else return false;
    }
}