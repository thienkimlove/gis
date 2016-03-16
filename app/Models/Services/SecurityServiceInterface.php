<?php

namespace Gis\Models\Services;

use Gis\Models\Entities\User;

/**
 * Interface for security service
 * Interface SecurityServiceInterface
 *
 * @package Gis\Models\Services
 */
interface SecurityServiceInterface {
	/**
	 * define login function
	 *
	 * @param array $data        	
	 */
	function authenticate($data);
	/**
	 * define logout function
	 */
	function logout();
	/**
	 * define check permission of user
	 *
	 * @param unknown $permission        	
	 */
	function authorize($permission);
	
	// check if user is authenticate or not?
	function isAuthenticate();
	
	/**
	 * define lock user if need
	 *
	 * @param string $username        	
	 */
	function isLockUser($username);
	
	/**
	 * define function filter user active
	 *
	 * @param User $user        	
	 */
	function filterUserActive(User $user);
	
	/**
	 * define function login for guest
	 *
	 * @param User $user        	
	 */
	function accessWithGuest();
}
