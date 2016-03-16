<?php
namespace Gis\Http\Controllers;

use Gis\Http\Requests\ForgetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Gis\Models\Services\OrganicMatterServiceFacade;
use Gis\Http\Requests\UserRegistrationRequest;
use Gis\Models\SystemCode;
use Gis\Http\Requests\UserModifyRequest;
use Gis\Http\Requests\UserDeleteRequest;
use Gis\Models\Services\UserService;
use Gis\Http\Requests\ChangingUserRequest;
use Gis\Http\Requests\ResetPasswordRequest;
use Gis\Http\Requests\AuthorizationRequest;
use Illuminate\Support\Facades\File;

class OrganicMatterController extends Controller
{


    /**
     * Open forget password screen
     *
     * @return Response
     */
    public function index()
    {
    }

    /**
     * Open form to select organic data by product
     * @return mixed
     */
    public function byProduct()
    {
        return view('admin.organicmatter.byproduct');
    }

    /**
     * Get organic data by product
     * @param Request $request
     * @return mixed
     */
    public function getDataByProduct(Request $request)
    {
        $postData = $request->all();
        $data = OrganicMatterServiceFacade::getDataByProduct($postData);
        if($data)
            return $data;
        else
            return response()->json(buildResponseMessage(trans('common.no_data_for_selection'), 1));
    }


    /**
     * Open form to select organic by green manure
     * @return mixed
     */
    public function greenManure()
    {
        return view('admin.organicmatter.greenmanure');
    }

    /**
     * get organic data by green manure
     * @param Request $request
     * @return mixed
     */
    public function getDataGreenManure(Request $request){
        $postData = $request->all();
        $data = OrganicMatterServiceFacade::getDataGreenManure($postData);
        if($data)
            return $data;
        else
            return response()->json(buildResponseMessage(trans('common.no_data_for_selection'), 1));
    }

    /**
     * Open form to select organic by compost
     * @return mixed
     */
    public function compost()
    {
        return view('admin.organicmatter.compost');
    }

    /**
     * Get organic data by compost
     * @param Request $request
     * @return mixed
     */
    public function getDataCompost(Request $request){
        $postData = $request->all();
        $data = OrganicMatterServiceFacade::getDataCompost($postData);
        return $data;
    }

    /**
     * Get organic data by fertilizer efficiency
     * @param Request $request
     * @return mixed
     */
    public function getDataFertilizerEfficiency(Request $request){
        $postData = $request->all();
        $data = OrganicMatterServiceFacade::getDataFertilizerEfficiency($postData);
        return $data;
    }
}