<?php

namespace Gis\Http\Controllers;

use Gis\Models\Services\FertilizerServiceFacade;
use Gis\Models\SystemCode;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Gis\Models\Services\FertilizationPriceServiceFacade;
use Gis\Http\Requests\FertilizationPriceRequest;
use Gis\Http\Requests;
use Gis\Http\Requests\FertilizerRequest;

/**
 * Class FertilizationPriceController
 * Use this class to handle all the businesses regarding the fertilizer price
 * @package Gis\Http\Controllers
 */
class FertilizationPriceController extends Controller
{
    /**
     * Display a listing of the fertilizers
     *
     * @return the view to be displayed
     */
    public function index()
    {
        //
        return view( 'admin.fertilizationprice.index');
    }

    /**
     * Show the form for creating a new fertilizer
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.fertilizationprice.create');
    }

    /**
     * Save the fertilizer price
     * @param FertilizationPriceRequest $request
     * @return the message that indicates the processing is successful
     */
    public function store(FertilizationPriceRequest $request)
    {
        $postData = $request->all();
        $check = FertilizationPriceServiceFacade::createFertilizationPrice($postData);
        if($check == 1)
            return response()->json(buildResponseMessage(trans('common.fertilization_price_create_success'),SystemCode::SUCCESS));
        else return response()->json(buildResponseMessage(trans('common.save_unsuccess')));
    }
   /**
     * Show the form for editing the specified fertilizer price.
     * @param $id the record id of fertilizer price
     * @return the fertilizer price record
     */
    public function edit($id)
    {
        $price = FertilizationPriceServiceFacade::findPriceById($id);
        return view('admin.fertilizationprice.edit', compact('price'));
    }

    /**
     * Update the specified fertilizer price
     * @param $id the id of record to be updated
     * @param FertilizationPriceRequest $request the posted data from browser
     * @return the message that indicates the processing is successful
     */
    public function update($id, FertilizationPriceRequest $request)
    {
        $price = FertilizationPriceServiceFacade::updatePrice($request->all(), $id);
        if($price == 1)
            return response()->json(buildResponseMessage(trans('common.fertilization_price_update_success'), 200, null, $price));
        else return response()->json(buildResponseMessage(trans('common.save_unsuccess')));
    }

    /**
     * get Grid Data.
     * @param Request $request the request to show all the fertilizers price
     * @return the list of fertilizer prices
     */
    public function priceGetGrid(Request $request) {

        $pagingRequest = $request->all();

        Paginator::currentPageResolver ( function () use($pagingRequest) {
            return $pagingRequest ['page'];
        } );
        return response ()->json ( FertilizationPriceServiceFacade:: gridGetAll('fertilizer_unit_price', $pagingRequest));
    }

    /**
     * Remove the specified fertilizer
     * @param Request $request
     * @return mixed
     */
    public function deleteFertilization(Request $request)
    {
        $postData = $request->input('ids');
        $retArray = FertilizationPriceServiceFacade::deleteFertilization($postData);
        if($retArray["inuse"] && $retArray["numberOfDeletedItems"] ==0){
            return response()->json(buildResponseMessage(trans('common.fertilization_price_delete_unsuccess'),200, 2000, null));
        }else{
            return response()->json(buildResponseMessage($retArray["numberOfDeletedItems"].":".trans('common.fertilization_price_delete_success'),200, 2000, null));
        }
    }

    /**
     * Processing after deleting fertilizer successfully
     * @param Request $request
     * @return mixed
     */
    public function afterDelete(Request $request) {

        $pagingRequest = $request->all();

        Paginator::currentPageResolver ( function () use($pagingRequest) {
            return $pagingRequest ['page'];
        } );
        return response ()->json ( FertilizationPriceServiceFacade:: gridGetAll('fertilizer_unit_price', $pagingRequest));
    }


}
