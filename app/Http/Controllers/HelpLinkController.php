<?php
namespace Gis\Http\Controllers;

use Gis\Models\Services\HelpLinkServiceFacade;
use Gis\Http\Requests\HelpLinkRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;


/**
 * Use this class to handle all the businesses regarding the content of help link screen
 * Class HelpLinkController
 * @package Gis\Http\Controllers
 */
class HelpLinkController extends CoreController{

    /**
     * Show the help link screen
     * @return the view that displays the help link screen
     */
	public function index() {

		return view('admin.helplink.index') ;
	}

    /**
     * Display the form to create new a help link
     * @return the view that displays the form to create new a help link
     */
	public function create(){

		return view('admin.helplink.create');
	}

    /**
     * Save the content of help link
     * @param HelpLinkRequest $request the information of help link
     * @return the message that indicates the processing is successful or not
     */
    public function store(HelpLinkRequest $request)
    {
        $helplink = HelpLinkServiceFacade::saveHelpLink($request->all());
        return response()->json(buildResponseMessage(trans('common.helplink_create_success'), 200, null, $helplink));
    }

    /**
     * Open a form to edit a help link content
     * @param $id the id of help link to edit
     * @return the view to display a form to edit help link content
     */
    public function edit($id)
    {
        $helplink = HelpLinkServiceFacade::findHelpLinkById($id);
        return view('admin.helplink.edit', compact('helplink'));
    }

    /**
     * Update the help link content to the database
     * @param $id the id of help link to edit
     * @param HelpLinkRequest $request the information of help link to edit
     * @return the message that indicates the process is successful or not
     */
    public function update($id, HelpLinkRequest $request)
    {
        $helplink = HelpLinkServiceFacade::updateHelpLink($request->all(), $id);

        return response()->json(buildResponseMessage(trans('common.helplink_update_success'), 200, null, $helplink));
    }

    /**
     * Delete a help link data from the database
     * @return the message that indicates the process is successful or not
     */
    public function deleteHelplink()
    {
        HelpLinkServiceFacade::deleteHelpLink(Request::input('ids'));
        return response()->json(buildResponseMessage(trans('common.helplink_delete_success'), 200, null, null));
    }

    /**
     * Display the data of help link under data grid format
     * @return the view that displays the help link
     */
	public function helpGrid()
	{
        $pagingRequest = Request::all();

        Paginator::currentPageResolver ( function () use($pagingRequest) {
            return $pagingRequest ['page'];
        } );
        $helpLinks=HelpLinkServiceFacade:: gridGetAll('helplink', $pagingRequest);
        foreach($helpLinks['rows'] as &$helpLink){
            switch($helpLink->popup_screen){
                case(0):{
                    $helpLink->popup_screen="";break;
                }
                case(1):{
                    $helpLink->popup_screen=trans('common.help_popup_1');break;
                }
                case(2):{
                    $helpLink->popup_screen=trans('common.help_popup_2');break;
                }
                case(3):{
                    $helpLink->popup_screen=trans('common.help_popup_3');break;
                }
                case(4):{
                    $helpLink->popup_screen=trans('common.help_popup_4');break;
                }
            }
        }
        return response ()->json ( $helpLinks);
	}

    /**
     * Show to content of help file to the browser
     * @param $file
     * @return mixed
     */
    public function view($file)
    {
        return view('admin.helplink.view ',compact('file'));
    }

    /**
     * Show to content of help file popup to the browser
     * @param Request $request
     * @internal param $file
     * @return mixed
     */
    public function getHelp()
    {
        $postData=$_POST;
        $helpFile= HelpLinkServiceFacade::getHelpUrl($postData);
        return $helpFile;
    }
}