<?php

namespace Gis\Http\Controllers;

use Gis\Http\Requests\ForgetPasswordRequest;
use Gis\Models\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Gis\Models\Services\UserServiceFacade;
use Gis\Http\Requests\UserRegistrationRequest;
use Gis\Models\SystemCode;
use Gis\Http\Requests\UserModifyRequest;
use Gis\Http\Requests\UserDeleteRequest;
use Gis\Models\Services\UserService;
use Gis\Http\Requests\ChangingUserRequest;
use Gis\Http\Requests\ResetPasswordRequest;
use Gis\Http\Requests\AuthorizationRequest;

class UserController extends Controller
{

    /**
     * Open forget password screen
     * @return Response
     */
    public function openForgetPassword()
    {
        return view('admin.authentications.password');
    }

    /**
     * Send the mail to user
     * @param ForgetPasswordRequest $request
     * @return Response
     */
    public function sendEmail(ForgetPasswordRequest $request)
    {
        $postData = $request->all();
        return UserServiceFacade::sendEmail($postData);        
    }

    /**
     * Open reset password screen
     * @param String $username
     * @param String $guid
     * @return Response
     */
    public function openResetPassword($username, $guid)
    {
        $isValidGuid = UserServiceFacade::checkGuid($username, $guid);
        if (!$isValidGuid) {
            return view('admin.users.changinguser2');
        }

        $data = array(
            'username' => $username,
            'guid' => $guid
        );
        return view('admin.authentications.reset', compact('data'));
    }

    /**
     * Reset the password of user
     * @param ResetPasswordRequest $request
     * @return Response
     */
    public function submitResetPassword(ResetPasswordRequest $request)
    {
        $postData = $request->all();
        $result = UserServiceFacade::updateResettingUser($postData);
        return $result;
    }

    /**
     * Open changing user screen
     */
    public function openChangingUser()
    {
        $user = UserServiceFacade::findUserById(session('user')->id);
        return view('admin.users.changinguser', compact('user'));
    }

    /**
     * Action submit changing user
     * @param ChangingUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitChangingUser(ChangingUserRequest $request)
    {

        $result = UserServiceFacade::updateChangingUser($request->all());
        // 1: success
        return $result;
        //return response()->json(buildResponseMessage(trans('common.changinguser_update_success'), 1));
    }
    /**
     * Confirm changing user screen
     */
    public function confirmChangingUser($username,$password,$email,$token)
    {
        $postData = array(
            'username'=>$username,
            'password'=>$password,
            'email'=>$email,
            'token'=>$token,
        );
        $result = UserServiceFacade::confirmChangingUser($postData);
        if(empty($result)){
            return redirect()->route('change-account')->with('status',trans('common.changinguser_update_success'));
        }
        return view('admin.users.changinguser2');
    }

    /**
     * Open authorization screen
     * @return Response
     */
    public function openAuthorization()
    {
        $userGroups = UserServiceFacade::getAuthorizationGroups();
        $permissions = UserServiceFacade::groupPermission();
        return view('admin.authentications.authorization', compact('userGroups', 'permissions'));
    }

    /**
     * Get data authorization
     * @param $groupId
     * @return Response
     */
    public function getAuthorizationGroup($groupId)
    {
        if ($groupId != 0) $group = UserServiceFacade::findGroupById($groupId);
        $array = [];
        foreach (UserServiceFacade::groupPermission() as $permission) {
            $array[] = array(
                'code' => $permission,
                'screen' => trans('common.' . $permission),
                'access' => empty($group) ? false : $group->$permission
            );
        }
        $jsonArray = json_encode($array);

        $result = "{\"rows\":" . $jsonArray . "}";
        return $result;
    }

    /**
     * Action submit authorization group
     * @param AuthorizationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitAuthorization(AuthorizationRequest $request)
    {
        $postData = $request->all();
        UserServiceFacade::updateAuthorization($postData);
        return response()->json(buildResponseMessage(trans('common.authorization_message_save_success')));
    }
    /**
     * Action list all user of system
     */
    public function index()
    {
        $userGroups = UserServiceFacade::getArrayGroups();
        $optionLocks = array(
            '' => trans('common.user_list_search_all_lock_option'),
            'f' => trans('common.user_list_search_not_lock_option'),
            't' => trans('common.user_list_search_lock_option')
        );

        return view('admin.users.index', compact('userGroups', 'optionLocks'));
    }

    /**
     * get Grid Data.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userGetGrid(Request $request) {
    
        $pagingRequest = $request->all();
    
        Paginator::currentPageResolver ( function () use($pagingRequest) {
            return $pagingRequest ['page'];
        } );
        return response ()->json ( UserServiceFacade:: gridGetAll('users', $pagingRequest));
    }
    
    /**
     * Search User by information
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function searchUser(Request $request)
    {
        $pagingRequest = $request->all();
        Paginator::currentPageResolver(function () use ($pagingRequest) {
            return $pagingRequest ['page'];
        });
        $postData = [
           "username" => $pagingRequest ['username'],
           "user_group_id" => $pagingRequest ['user_group_id'],
            "user_locked_flg" => $pagingRequest ['user_locked_flg'],
            "user_code" => $pagingRequest ['user_code'],
            "email" =>UserServiceFacade::encode($pagingRequest ['email'])
        ];
        return response()->json(UserServiceFacade:: gridGetAll('users', $pagingRequest, $postData));
    
    }
    
    
    /**
     * View Form create new User
     *
     */
    public function create()
    {
        $userGroups = UserServiceFacade::getArrayGroups();
        $systemNumbers = UserServiceFacade::getSystemNumbers();
        return view('admin.users.create', compact('userGroups','systemNumbers'));
    }

    /**
     * Action Save User
     * @param UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRegistrationRequest $request)
    {
        UserServiceFacade::createUser($request->all());
        $responseData = buildResponseMessage(trans('common.user_registration_user_success'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * View user information
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = UserServiceFacade::findUserById($id);
        $userGroups = UserServiceFacade::getArrayGroups($user);
        $systemNumbers = UserServiceFacade::getSystemNumbers();

        return view('admin.users.edit', compact('user', 'userGroups','systemNumbers'));
    }

    /**
     * Action Edit user
     * @param UserModifyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserModifyRequest $request, $id)
    {
        UserServiceFacade::editUser($request->all(), $id);
        $responseData = buildResponseMessage(trans('common.user_edit_user_success'), SystemCode::SUCCESS);
        return response()->json($responseData);
    }

    /**
     * Action Delete user
     * @param UserDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(UserDeleteRequest $request)
    {
        $countUser = UserServiceFacade::deleteUser($request->input('ids'));
        if ($countUser == 0)
            $responseData = buildResponseMessage(trans('common.user_can_delete'));
        else $responseData = buildResponseMessage(trans('common.user_delete_user_success') . ":" . $countUser . " Users", SystemCode::SUCCESS);
        return response()->json($responseData);
    }
}
