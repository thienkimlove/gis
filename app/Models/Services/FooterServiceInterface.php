<?php
namespace Gis\Models\Services;

/**
 * The interface for all classes that implement business logic regarding footer content
 * Interface FooterServiceInterface
 * @package Gis\Models\Services
 */
interface FooterServiceInterface{

	function saveFooter(array $data);
	function loadFooter();

}