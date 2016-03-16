<?php namespace Gis\Http\Controllers;

use Gis\Http\Requests;
use Gis\Http\Requests\GroupRequest;
use Gis\Models\Services\UserServiceFacade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;

/**
 * User this class to handle all the businesses regarding the user group
 * Class GroupsController
 * @package Gis\Http\Controllers
 */

class GroupsController extends CoreController {

   	/**
	 * Display a listing of the user groups
	 *
	 * @return Response
	 */
	public function index()
	{
        return view('admin.usergroup.index');
	}

	/**
	 * Show the form for creating a new user group.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.usergroup.create');
	}

    /**
     * Create new an user group
     *
     * @param GroupRequest $request the information of user group
     * @return the message that indicates the processing is successful or not
     */
	public function store(GroupRequest $request)
	{
	   $group =	UserServiceFacade::createGroup($request->all());
       return response()->json(buildResponseMessage(trans('common.usergroup_create_success'), 200, null, $group));
	}

	/**
	 * Show the form for editing the user group.
	 *
	 * @param  int  $id of user group
	 * @return the information of user group
	 */
	public function edit($id)
	{
        $group = UserServiceFacade::findGroupById($id);
        return view('admin.usergroup.edit', compact('group'));
	}

    /**
     * Update the specified user group
     *
     * @param  int $id id of user group to update
     * @param GroupRequest $request the user group information to update
     * @return the message that indicates the processing is successful or not
     */
	public function update($id, GroupRequest $request)
	{
        $group = UserServiceFacade::updateGroup($request->all(), $id);

        return response()->json(buildResponseMessage(trans('common.usergroup_update_success'), 200, null, $group));
	}

    /**
     * Remove the specified user group from database
     * @return the message that indicates the processing is successful or not
     * @internal param int $id
     */
	public function deleteGroup()
	{
        $delGroup =  UserServiceFacade::deleteGroup(Request::input('ids'));
        if ($delGroup == 1)
            return response()->json(buildResponseMessage(trans('common.usergroup_delete_success'),200));
        else if($delGroup == 0)
            return response()->json(buildResponseMessage(trans('common.usergroup_can_not_delete'),2000));
        else return response()->json(buildResponseMessage(trans('common.user_can_not_delete_guest_group'),2000));
    }

    /**
     * Show the list of user groups to the browser
     * @return list of user groups under data grid format
     */
    public function groupGrid() {

        $pagingRequest = Request::all();

        Paginator::currentPageResolver ( function () use($pagingRequest) {
            return $pagingRequest ['page'];
        } );
        return response ()->json ( UserServiceFacade:: gridGetAll('groups', $pagingRequest));
    }

    /**
     * Get Administrator user group
     * @return the information for Administrator group
     */
    public function getAdminGroup(){
        
        return UserServiceFacade::getAdminGroup(); 
    }
    
}
