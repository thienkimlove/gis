<?php
namespace Gis\Models;

/**
 * Defines all operation result codes to use in the system.
 *
 * @author HaLM
 *        
 */
class SystemCode
{
    /*--------------------------------------*/
    /* SYSTEM & HTTP-SPECIFIC               */
    /*--------------------------------------*/
    
    /**
     * Succeeded, no error.
     */
    const SUCCESS = 200;
    
    /**
     * Object creation succeeded.
     */
    const RESOURCE_CREATED = 201;
    
    /**
     * Request accepted, not yet completed.
     */
    const ACCEPTED = 202;
    
    /**
     * Not modified
     */
    const NOT_MODIFIED = 304;
    
    /**
     * The given request is incorrect.
     */
    const BAD_REQUEST = 400;
    
    /**
     * The given request requires payment to be proceeded first.
     */
    const PAYMENT_REQUIRED = 402;
    
    /**
     * Unauthorized.
     */
    const UNAUTHORIZED = 401;
    
    /**
     * Not allowed to perform action or session expired.
     */
    const PERMISSION_DENIED = 403;
    
    /**
     * The needed resource could not be found.
     */
    const NOT_FOUND = 404;
    
    /**
     * Requested method is not allowed.
     */
    const METHOD_NOT_ALLOWED = 405;
    
    /**
     * There are conflicts in the request.
     */
    const CONFLICT = 409;
    
    /**
     * The server does not meet one of requirements put in the request.
     */
    const PRECONDITION_FAILED = 412;
    
    /**
     * Request entity too large.
     */
    const REQUEST_ENTITY_TOO_LARGE = 413;
    
    /**
     * Unsupported media type.
     */
    const UNSUPPORTED_MEDIA_TYPE = 415;
    
    /**
     * Resource Locked.
     */
    const RESOURCE_LOCKED = 423;
    
    /**
     * Unavailable For Legal Reasons
     */
    const UNAVAILABLE_FOR_LEGAL_REASON = 451;
    
    /**
     * Internal unhanled error occurs.
     */
    const UNHANDLED_ERROR = 500;
    
    /**
     * Database error occurs.
     */
    const DB_ERROR = 503;
}
