<?php

namespace Gis\Models\Services;

/**
 * Using for declaring methods list for database business layer.
 * Interface UserServiceInterface
 *
 * @package Gis\Models\Services
 */
interface UserServiceInterface extends BaseServiceInterface {

    function getAllGroups();
    function getUserState($user_code);
    function updateStateOfUser($postData, $user_code);
    function groupPermission();
    //groups
	function findGroupById($groupId);
    function createGroup(array $group);
    function updateGroup(array $attributes, $id);
    function deleteGroup(array $ids);

    //users
    function getArrayGroups($user);
    function findUserById($userId);
    function createUser($postData);
    function editUser($postData, $id);
    function deleteUser($ids);
    function findUserByKeyword($keyword);


    //some other need refactor.
    function sendEmail(array $postData);
    function findUserByEmail($email);
	function findUserByUsername($username);
	function checkGuid($username, $guid);
	function updateGuidUser($email, $guid);
	function updateAuthorization(array $postData);
	function updateChangingUser(array $postData);
	function updateResettingUser(array $postData);
	function getUserWithNormal();
	function getAuthorizationGroups();

    function saveAgreed($id, $attributes);

}
