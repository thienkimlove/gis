<?php
namespace Gis\Models\Services;

use Carbon\Carbon;
use Gis\Models\Repositories\FertilizationPriceFacade;
use Gis\Services\Logging\ApplicationLogFacade;
use Gis\Exceptions\GisException;
use Gis\Helpers\LoggingAction;
use Gis\Helpers\DataHelper;

/**
 * The service to handle business regarding fertilizer price
 * Methods to work with repositories.
 * Class FertilizationPriceService
 *
 * @package Gis\Models\Services
 */
class FertilizationPriceService extends BaseService implements FertilizationPriceServiceInterface
{
    /**
     * Create new unit price for fertilizer
     *
     * @param array $data
     * @return mixed
     * @throws GisException
     * @internal param array $permissions
     * @internal param array $fertilization
     */
    function createFertilizationPrice($postData)
    {
        if($postData['end_date']){
            $isValid = $this->isValid(0,$postData['start_date'],$postData['end_date']);
            $attributes = array(
                'price' => $postData['price'],
                'start_date' => $postData['start_date'],
                'end_date' =>  $postData['end_date'],

            );
        }else{
            $isValid = $this->isValid(0,$postData['start_date'],'');
            $attributes = array(
                'price' => $postData['price'],
                'start_date' => $postData['start_date']
            );
        }
        if($isValid){
            $attributes = $this->modifyData($attributes,true);
            FertilizationPriceFacade::create($attributes);
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_PRICE_UNIT_OF_FERTILIZER_MAP, $attributes);
            return 1;
        }else return 0;

    }


    /**
     * Delete fertilization price by array of ids.
     *
     * @param array $ids
     * @return mixed void
     * @throws GisException
     */
    function deleteFertilization(array $ids)
    {
        $isInuse = false;
        $count=0;
        $now = date_create(Carbon::now()->format('Y-m-d 00:00:00'));
        foreach($ids as $key){
            $row = FertilizationPriceFacade::findByField('id', $key)->first();
            if(is_null($row)){
                continue;
            }
            if(is_null($row->end_date) && $now >=date_create($row->start_date) )
            {
                $isInuse = true;
                continue;
            }
            if(date_create($row->end_date) >= $now && date_create($row->start_date) <=$now){
                $isInuse = true;
                continue;
            }
            else{
                $count++;
                FertilizationPriceFacade::delete($key);
                ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DELETE_PRICE_UNIT_OF_FERTILIZER_MAP, $key);
            }
        }
        return array(
            'inuse' => $isInuse,
            'numberOfDeletedItems' => $count

        );
    }

    /**
     * Find Price by Id
     *
     * @param
     *            $id
     * @return mixed
     *
     * @throws GisException
     */
    function findPriceById($id)
    {
        $price = FertilizationPriceFacade::getById($id);
        if ($price) {
            $startdate = explode(" ", $price->start_date);
            $enddate = explode(" ", $price->end_date);
            $price->start_date = $startdate[0];
            $price->end_date = $enddate[0];
            return $price;
        } else {
            throw new GisException(trans('common.fertilization_price_id_not_existed'));
        }
    }

    /**
     * Update price by price id.
     *
     * @param array $attributes
     * @param
     *            $id
     * @return mixed
     *
     * @throws GisException
     */
    function updatePrice(array $attributes, $id)
    {
        $isValid = $this->isValid($id,$attributes['start_date'],$attributes['end_date']);
        if($isValid){
            $attributes = $this->modifyData($attributes);
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_PRICE_UNIT_OF_FERTILIZER_MAP, $attributes);
            if(empty($attributes['end_date']))
            {
                $attributes['end_date'] = null;
            }
            FertilizationPriceFacade::update($attributes, $id);
            return 1;
        }else return 0;
    }

    /**
     * check date of unit price have satisfying
     * @param $id
     * @param $startDate
     * @param $endDate
     * @return bool
     */
    public function isValid($id,$startDate, $endDate){
        $startDate = date_create($startDate);
        $endDateDb = FertilizationPriceFacade::getAllDate($id);
        if(!empty($endDate)){
            $endDate = date_create($endDate);
        }else{
            //no end date, start date must be greater than end date of other item
            foreach($endDateDb as $row) {
                if (
                    $startDate <= date_create($row->end_date)
                ){
                    return false;
                }
            }
        }

        if(!empty($endDate)){
            //start date must be less than end date
            if($startDate>$endDate)
                return false;
            //if exists one record with empty end date
            foreach($endDateDb as $row) {
                if (is_null($row->end_date)
                    &&
                    $endDate >=date_create($row->start_date)
                ){
                    return false;
                }
            }
            //end date cannot be less than current date
            if($endDate<date_create(date('Y-m-d'))){
                return false;
            }
            //start date must be greater than end date of other item
            foreach($endDateDb as $row) {
                if (
                    $endDate <= date_create($row->end_date)&&$endDate >= date_create($row->start_date)
                    ||
                    $startDate <= date_create($row->end_date)&&$startDate >= date_create($row->start_date)
                ){
                    return false;
                }
            }
        }
        else{
            //user enters empty end date
            foreach($endDateDb as $row) {
                if (is_null($row->end_date)){
                    return false;
                }
                else{

                }

            }
        }
        return true;
    }

}