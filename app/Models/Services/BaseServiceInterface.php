<?php

/**
 * Some useful functions can be using at any services.
 */

namespace Gis\Models\Services;
/**
 * Base interface for Base service class
 * Interface BaseServiceInterface
 * @package Gis\Models\Services
 */
interface BaseServiceInterface {

    function modifyData(array $attributes, $create);
    function gridGetAll($default, $pagingRequest, $postData);

}