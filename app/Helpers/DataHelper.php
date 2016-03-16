<?php
namespace Gis\Helpers;
/**
 * Created by PhpStorm.
 * User: namdv
 * Date: 9/8/2015
 * Time: 12:24 PM
 */
use Carbon\Carbon;
class DataHelper
{
    public static function getDateOnly($date){
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    /**
     * Convert System number of user to coordinate number
     */
    public static function getCoordinates($userSystemNumber){
        switch($userSystemNumber){
            case 1:
                return "2443";
                break;
            case 2:
                return "2444";
                break;
            case 3:
                return "2445";
                break;
            case 4:
                return "2446";
                break;
            case 5:
                return "2447";
                break;
            case 6:
                return "2448";
                break;
            case 7:
                return "2449";
                break;
            case 8:
                return "2450";
                break;
            case 9:
                return "2451";
                break;
            case 10:
                return "2452";
                break;
            case 11:
                return "2453";
                break;
            case 12:
                return "2454";
                break;
            case 13:
                return "2455";
                break;
            case 14:
                return "2456";
                break;
            case 15:
                return "2457";
                break;
            case 16:
                return "2458";
                break;
            case 17:
                return "2459";
                break;
            case 18:
                return "2460";
                break;
            case 19:
                return "2461";
                break;
            default:
                return "2455";
        }
    }


}