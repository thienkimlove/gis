<?php
namespace Gis\Models\Services;

/**
 * The interface for the classes that implement the business logic regarding help link data
 * Interface HelpLinkServiceInterface
 * @package Gis\Models\Services
 */
interface HelpLinkServiceInterface{
	function saveHelpLink(array $data);
	
	function findHelpLinkById($id);
	
	function deleteHelpLink($ids);
	
	function updateHelpLink(array $data, $id);
    function findHelpLinkByAdd($url);

}