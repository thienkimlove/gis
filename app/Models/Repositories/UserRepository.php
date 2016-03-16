<?php

namespace Gis\Models\Repositories;

/**
 * User Repository interface, provider object to access data provider.
 * Interface UserRepository
 *
 * @package namespace Gis\Models\Repositories;
 */
interface UserRepository extends GisRepositoryInterface
{
    function checkLogin(array $data);
	public function getUserLogDataByIds($ids, $columns = array('*'));
	public function getAccountGuest();
	public function getWithOutAdminAndGuest($keyword);
	function getSpecifyUsers($limit, $orderBy, $orderType);
}