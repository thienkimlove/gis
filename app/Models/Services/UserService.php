<?php
namespace Gis\Models\Services;

use Carbon\Carbon;
use Gis\Models\Repositories\GroupFacade;
use Gis\Models\Repositories\StateOfUserFacade;
use Gis\Models\Repositories\UserFacade;
use Gis\Models\Entities\User;
use Gis\Exceptions\GisException;
use Gis\Exceptions\DBApplicationException;
use Gis\Services\Logging\ApplicationLogFacade;
use Gis\Models\SystemCode;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Gis\Helpers\LoggingAction;
use Illuminate\Support\Facades\Session;

/**
 * Methods to work with repositories.
 * Class UserService
 *
 * @package Gis\Models\Services
 */
class UserService extends BaseService implements UserServiceInterface
{

    /**
     * Delimiter for encrypt password
     *
     * @var ENABLE_ENCRYPT_PASSWORD
     */
    const DELIMITER_ENCRYPT_PASSWORD = '|';

    /**
     * Action Edit Log
     *
     * @var EDIT_ACTION
     */
    const EDIT_ACTION = 'edit';

    /**
     * Action Delete Log
     *
     * @var DELETE_ACTION
     */
    const DELETE_ACTION = 'delete';

    /**
     * Get All USer Groups
     */
    public function getAllGroups()
    {
        return GroupFacade::all();
    }

    /**
     * return real field name for permission base on key.
     *
     * /**
     * Get User State
     */
    function getUserState($user_code)
    {
        $state = StateOfUserFacade::findByField('user_code', $user_code)->first();
        return $state;
    }

    /**
     * Modify state information
     *
     * @param array $postData
     * @param
     *            $id
     * @return bool
     * @throws GisException
     * @internal param User $user
     */
    function updateStateOfUser($postData, $user_code)
    {
        $attributes['last_active_layer_id'] = $postData['last_active_layer_id'];
        $attributes['user_code'] = $postData['user_code'];
        $state = StateOfUserFacade::findByField('user_code', $user_code)->first();
        if (empty($state)) {
            $attributes = $this->modifyData($attributes, true);
            return StateOfUserFacade::create($attributes);
        } else {
            $attributes = $this->modifyData($attributes);
            return StateOfUserFacade::update($attributes, $state->id);
        }
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_USER_CHANGE_STATE,$attributes );
    }

    /**
     * return real field name for permission base on key.
     *
     * @param null $permission
     * @param null $revert
     * @return array
     */
    public function groupPermission()
    {
        return array(
            'auth_user_registration',
            'auth_change_username_password',
            'auth_user_group',
            'auth_authorization',
            'auth_folder_layer',
            'auth_user_fertilizer_definition',
            'auth_help',
            'auth_footer',
            'auth_fertilizer_price',
            'auth_purchasing_management',
        );
    }

    /**
     * FInd Group by Id
     *
     * @param
     *            $id
     * @return mixed
     *
     * @throws GisException
     */
    function findGroupById($id)
    {
        $group = GroupFacade::getById($id);
        if ($group) {
            return $group;
        } else {
            throw new GisException(trans('common.usergroup_id_not_existed'));
        }
    }

    /**
     * Create new user group
     *
     * @param array $data
     * @return mixed
     * @throws GisException
     * @internal param array $permissions
     * @internal param array $group
     */
    function createGroup(array $data)
    {
        if (GroupFacade::findByField('group_name', $data['group_name'])->first()) {
            throw new GisException(trans('common.usergroup_create_group_name_exits'));
        }

        foreach ($this->groupPermission() as $permission) {
            $data[$permission] = false;
        }
        $data['is_guest_group'] = ! empty($data['is_guest_group']) && $data['is_guest_group'] == 'on' ? true : false;
        if($data['is_guest_group']){
            $checkExistGuestGroup = GroupFacade::checkExistGuestUser();
            if ($checkExistGuestGroup) {
                throw new GisException(trans('common.user_can_not_create_guest_group'));
            }
            $array = array('can_delete' => false);
            $data = array_merge($data, $array);
        }
        $data["auth_user_fertilizer_definition"] = false;

        $data = $this->modifyData($data, true);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_ADD_USER_GROUP, $data);

        return GroupFacade::create($data);
    }

    /**
     * Update group by group id.
     *
     * @param array $attributes
     * @param
     *            $id
     * @return mixed
     *
     * @throws GisException
     */
    function updateGroup(array $attributes, $id)
    {
        $currentGroup = $this->findGroupById($id);

        if ($attributes['group_name'] != $currentGroup->group_name && GroupFacade::findByField('group_name', $attributes['group_name'])->first()) {
            throw new GisException(trans('common.usergroup_create_group_name_exits'));
        }
        $attributes['is_guest_group'] = ! empty($attributes['is_guest_group']) && $attributes['is_guest_group'] == 'on' ? true : false;
        if($attributes['is_guest_group']){
            $checkExistGuestGroup = GroupFacade::checkExistGuestUser();
            if ($checkExistGuestGroup) {
                throw new GisException(trans('common.user_can_not_create_guest_group'));
            }
        }
        $attributes = $this->modifyData($attributes);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_USER_GROUP, $attributes);
        return GroupFacade::update($attributes, $id);
    }

    /**
     * Delete groups by array of ids.
     *
     * @param array $ids
     * @return mixed void
     * @throws GisException
     */
    function deleteGroup(array $ids)
    {
        try {
            foreach($ids as $id){
                $group = GroupFacade::findByField('id', $id)->first();
                if($group->can_delete === false)
                    return 0;
                if($group->is_guest_group === true){
                    return -1;
                }

            }
            GroupFacade::deleteMany($ids);
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DELETE_USER_GROUP, $ids);
            return 1;
        } catch (\PDOException $e) {
            if ($e->getCode() == DBApplicationException::FOREIGN_KEY_EX_CODE) {
                throw new GisException(trans('common.usergroup_delete_foreign_key'), $e->getCode());
            } else {
                return response()->json(buildResponseMessage($e->getMessage()));
            }
        }
    }

    /**
     * Get the admin group information
     * @return mixed
     */
    function getAdminGroup()
    {
        try {
            return GroupFacade::selectModel()->where('auth_authorization', '=', true)
                ->lists('id')
                ->all();
        } catch (\PDOException $e) {
            return response()->json(buildResponseMessage($e->getMessage()));
        }
    }

    /**
     * ************************************************************************************
     * Users CURD Functions
     * ***********************************************************************************
     */

    /**
     * Get groups list for select box
     *
     * @param null $user
     * @return int
     */
    function getArrayGroups($user = null)
    {
        if (! $user) {
            return array(
                '' => trans('common.user_registration_search_group_option_default')
            ) + GroupFacade::lists('group_name', 'id')->all();
        }
        $canChangeType = ($user->usergroup->auth_authorization || $user->usergroup->is_guest_group) ? false : true;
        if ($canChangeType) {
            return GroupFacade::whereConditions(array(
                array(
                    'auth_authorization',
                    '!=',
                    true
                ),
                array(
                    'is_guest_group',
                    '!=',
                    true
                )
            ))->lists('group_name', 'id')->all();
        }
        return null;
    }

    /**
     * Find User By Id
     *
     * @param
     *            $id
     * @return mixed
     *
     * @throws GisException
     */
    function findUserById($id)
    {
        $user = UserFacade::getById($id);
        if ($user) {
            $user->email = $this->decode($user->email);
            return $user;
        } else {
            throw new GisException(trans('common.user_not_exists'));
        }
    }

    /**
     * Create new user
     *
     * @param array $postData
     * @return bool
     * @throws GisException
     */
    function createUser($postData)
    {
        $check = false;
        $checkGuestGroup = GroupFacade::findByField('is_guest_group', true)->first();
        if($checkGuestGroup){
            $checkExistGuestUser = UserFacade::findByField('user_group_id', $checkGuestGroup->id)->first();
            if ($checkExistGuestUser && $postData['user_group_id'] == $checkGuestGroup->id) {
                throw new GisException(trans('common.user_can_not_create_guest_user'), SystemCode::CONFLICT);
            }
            if($postData['user_group_id'] == $checkGuestGroup->id) $check=true;
        }

        if (UserFacade::findByField('username', $postData['username'])->first()) {
            throw new GisException(trans('common.user_registration_username_exists'), SystemCode::CONFLICT);
        }

        if (UserFacade::findByField('email', $this->encode($postData['email']))->first()) {
            throw new GisException(trans('common.user_registration_email_exists'), SystemCode::CONFLICT);
        }

        $attributes = array(
            'username' => $postData['username'],
            'password' => SecurityService::ENABLE_ENCRYPT_PASSWORD ? encryptString($postData['password']) : $postData['password'],
            'email' => $this->encode($postData['email']),
            'user_group_id' => $postData['user_group_id'],
            'user_locked_flg' => empty($postData['user_locked_flg']) ? false : $postData['user_locked_flg'],
            'login_failed_count' => 1,
            'is_agreed' => false,
            'last_logout_time' => null,
            'token' => null,
            'system_number' => empty($postData['system_number']) ? null : $postData['system_number']
        );

        if($check==true){
            $array = array('can_delete' => false);
            $attributes = array_merge($attributes,$array);
        }

        $attributes = $this->modifyData($attributes, true);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_REGISTER_USER, $attributes);
        return UserFacade::create($attributes);
    }

    /**
     * Update Agreed Attribute of user.
     *
     * @param
     *            $id
     * @param
     *            $attributes
     * @return mixed
     */
    function saveAgreed($id, $attributes)
    {
        if($attributes["is_agreed"])
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_AGREE_TERM, $attributes);
        else
            ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_DONNOT_AGREE_TERM, $attributes);
        return UserFacade::update($attributes, $id);
    }

    /**
     * Modify user's information
     *
     * @param array $postData
     * @param
     *            $id
     * @return bool
     * @throws GisException
     * @internal param User $user
     */
    function editUser($postData, $id)
    {
        $currentUser = $this->findUserById($id);
        if (! empty($postData['username'])) {
            if ($postData['username'] != $currentUser->username && UserFacade::findByField('username', $postData['username'])->first()) {
                throw new GisException(trans('common.user_registration_username_exists'));
            }
            $attributes['username'] = $postData['username'];
        }
        if (! empty($postData['email'])) {
            if ($postData['email'] != $currentUser->email && UserFacade::findByField('email', $this->encode($postData['email']))->first()) {
                throw new GisException(trans('common.user_registration_email_exists'));
            }
            $attributes['email'] = $this->encode($postData['email']);
        }

        $attributes['user_locked_flg'] = empty($postData['user_locked_flg']) ? false : true;
        if(!$attributes['user_locked_flg']){
           $attributes['login_failed_count'] = 0;
        }

        if (! empty($postData['user_group_id'])) {
            $group = $this->findGroupById($postData['user_group_id']);
            if (! $group->auth_authorization && ! $group->is_guest_group) {
                $attributes['user_group_id'] = $postData['user_group_id'];
            }
        }

        if (! empty($postData['password'])) {
            $attributes['password'] = SecurityService::ENABLE_ENCRYPT_PASSWORD ? encryptString($postData['password']) : $postData['password'];
        }

        if (! empty($postData['system_number'])) {
            $attributes['system_number'] = $postData['system_number'];
        }

        $attributes = $this->modifyData($attributes);
        ApplicationLogFacade::logAction(LoggingAction::MODE2_UPDATE_USER, $attributes);

        return UserFacade::update($attributes, $id);
    }

    /**
     * Delete user from database
     *
     * @param
     *            $ids
     * @return int
     * @throws GisException
     */
    function deleteUser($ids)
    {
        try {
            $loginId = session('user')->id;
            if (in_array($loginId, $ids))
                throw new GisException(trans('common.user_delete_not_self'), SystemCode::BAD_REQUEST);
            $userLogDatas = UserFacade::getUserLogDataByIds($ids, array(
                'user_code',
                'username'
            ));
            $logData = array(
                'Action' => LoggingAction::MODE2_DELETE_USER,
                'User Action' => array(
                    'user_code' => session('user')->user_code
                ),
                'User' => array()
            );

            foreach($ids as $id){
                $check = UserFacade::findByField('id', $id)->first();
                $checkGuest = GroupFacade::findByField('id', $check->user_group_id)->first();
                if($checkGuest->is_guest_group)
                    throw new GisException(trans('common.user_can_not_delete_guest_user'));
            }
            $user = UserFacade::selectModel()->whereIn('id', $ids)
                ->where('can_delete', '=', false)
                ->first();
            if ($user) {
                return 0;
            }

            if (! $userLogDatas->isEmpty()) {
                foreach ($userLogDatas as $user) {
                    $item = array(
                        'username' => $user->username,
                        'user_code' => $user->user_code
                    );
                    array_push($logData['User'], $item);
                }
            }

            UserFacade::deleteMany($ids);

            ApplicationLogFacade::logAction(LoggingAction::MODE2_DELETE_USER, $userLogDatas);

            return count($ids);
        } catch (\PDOException $e) {
            if ($e->getCode() == DBApplicationException::FOREIGN_KEY_EX_CODE)
                throw new GisException(trans('common.user_delete_foreign_key'), $e->getCode());
            else
                throw new \PDOException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * Update authorization for group.
     *
     * @param array $postData
     * @return mixed
     *
     * @throws GisException
     */
    function updateAuthorization(array $postData)
    {
        $attributes = $this->modifyData($postData);
        $group = GroupFacade::getById($postData['group_id']);
        if ($group == null) {
            throw new GisException(trans('common.authorization_usergroup_not_exists'));
        }
        $result = GroupFacade::update($attributes, $postData['group_id']);

        $user = session('user');
        if ($user->user_group_id == $postData['group_id']) {
            $user = UserFacade::findById($user->id);
            if ($user == null) {
                throw new GisException(trans('common.authorization_usergroup_not_exists'));
            }

            session('user', $user);
        }
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_AUTHORIZATION, $result);
        return $result;
    }

    /**
     * Update changing user
     *
     * @param array $postData
     * @return mixed
     *
     *
     * @throws GisException
     */
    function updateChangingUser(array $postData)
    {

        $user = session('user');
        $emailUser = UserFacade::findByField('email', $this->encode($postData['email']))->first();
        if ($emailUser != null && $emailUser->id != $user->id) {
            throw new GisException(trans('common.changinguser_duplicate_email'));
        }

        $usernameUser = UserFacade::findByField('username', $postData['username'])->first();
        if ($usernameUser != null && $usernameUser->id != $user->id) {
            throw new GisException(trans('common.changinguser_duplicate_username'));
        }

        $oldUser = UserFacade::getById($user->id);

        if ($oldUser == null) {
            throw new GisException(trans('common.changinguser_user_not_exists'));
        }
        $newUser = UserFacade::with('usergroup')->getById($user->id);

        if ($newUser == null) {
            throw new GisException(trans('common.changinguser_user_not_exists'));
        }
        $guid = getGuid();
        $attributes = array();
        $attributes['token'] = $guid;

        $attributes = $this->modifyData($attributes);
        UserFacade::update($attributes, $user->id);

        $newUser->username =  !empty($postData['username']) ? $postData['username'] : $newUser->username;
        $newUser->password = !empty($postData['password']) ? $postData['password'] : $newUser->password;
        $newUser->email =  !empty($postData['email']) ? $postData['email'] : $this->decode($newUser->email);
        $newUser->token =  $guid;
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_CHANGE_USERNAME_PASSWORD, $newUser);
        $confirm_link = url('/users/confirm-change-account/');
        session(['email'=>$newUser->email]);

        $data = array(
            'name' => $user->username,
            'confirm_link' => $confirm_link
        );
            try {
                Mail::send('emails.mail-changing-user', array(
                    'data' => $data,
                    'user' => $newUser
                ), function ($message)
                {
                    $message->to(session('email'), 'Nothing')->subject(trans('common.changing_mailer_subject'));
                });
            } catch (\Exception $e) {
                // die($e);
                return response()->json(buildResponseMessage($e));
            }
        // return $result;
        return response()->json(buildResponseMessage(trans('common.changinguser_update_success2'), 1));
    }

    /**
     * Confirm to change user information
     * @param array $postData
     * @return mixed
     */
     function confirmChangingUser(array $postData)
    {
        if (empty($postData['token']) || empty($postData['username']) || empty($postData['password'])  || empty($postData['email'])) {
            return trans('common.changinguser_user_not_exists');
        }
        $user = UserFacade::findByField('token', $postData['token'])->first();

        if (empty($user)) {
            return trans('common.changinguser_user_not_exists');
        }
        $emailUser = UserFacade::findByField('email', $this->encode($postData['email']))->first();

        if ($emailUser != null && $emailUser->id != $user->id) {
            return trans('common.changinguser_duplicate_email');
        }

        $usernameUser = UserFacade::findByField('username', $postData['username'])->first();
        if ($usernameUser != null && $usernameUser->id != $user->id) {
            return trans('common.changinguser_duplicate_username');
        }

        $oldUser = UserFacade::getById($user->id);

        if ($oldUser == null) {
            return trans('common.changinguser_user_not_exists');
        }


        $newUser = UserFacade::with('usergroup')->getById($user->id);
        if (empty($newUser)) {
            return trans('common.changinguser_user_not_exists');
        }
        $attributes = array(
            'token' => null,
            'password' => $postData['password'],
            'email' => $this->encode($postData['email']),
            'username' => $postData['username'],
        );
        $attributes = $this->modifyData($attributes);
        $result = UserFacade::update($attributes, $user->id);
         $newUser = UserFacade::with('usergroup')->getById($user->id);
        session(array(
            'user' => $newUser
        ));

        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_CHANGE_USERNAME_PASSWORD, $newUser);
    }

    /**
     * Send the mail to user
     *
     * @param ForgetPasswordRequest $request
     * @return Response
     */
    public function sendEmail(array $postData)
    {
        $email = $postData['email'];
        session(array(
            'email' => $email
        ));
        $user = UserFacade::findByField('email', $this->encode($postData['email']))->first();
        if ($user == null) {
            return response()->json(buildResponseMessage(trans('common.forgetpassword_email_not_found')));
        }

        $guid = getGuid();
        $link = url() . '/reset-password/' . $user->username . '/' . $guid;
        $data = array(
            'name' => $user->username,
            'link' => $link
        );

        try {
            Mail::send('emails.mailer', array(
                'user' => $data
            ), function ($message)
            {
                $message->to(session('email'), 'Nothing')->subject(trans('common.mailer_subject'));
            });

            $userDataUpdate = array(
                'token' => $guid
            );

            $result = UserFacade::update($userDataUpdate, $user->id);
        } catch (\Exception $e) {
            return response()->json(buildResponseMessage($e));
        }
        return response()->json(buildResponseMessage(trans('common.forgetpassword_email_sent'), 1));
    }

    /**
     * Update resetting user
     *
     * @param array $postData
     * @return mixed
     * @throws GisException
     * @internal param $
     */
    function updateResettingUser(array $postData)
    {
        $user = UserFacade::findByField('username', $postData['username'])->first();


        if($postData['password']==$user->username){
            throw new GisException(trans('common.user_registration_password_username_equals'));
        }

        if ($user == null || $user->token != $postData['guid'] || $user->token == '') {
            throw new GisException(trans('common.resetpassword_url_invalid'));
        }

        $attributes = array(
            'user_locked_flg'=>false,
            'login_failed_count'=>0,
            'is_agreed'=>false,
            'password' => $postData['password'],
            'token' => null
        );

        $attributes['upd_user'] = $user->user_code;
        $attributes['upd_time'] = Carbon::now()->format('Y-m-d H:i:s');

        UserFacade::update($attributes, $user->id);
        Session::put ( 'usercode', $user->user_code );
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_CHANGE_USERNAME_PASSWORD, $user);

        return response()->json(buildResponseMessage(trans('common.resetpassword_password_change_success'), 1));
    }

    /**
     * Update the guid for user by email.
     *
     * @param email $email
     * @param
     *            $guid
     * @return mixed
     */
    function updateGuidUser($email, $guid)
    {
        $user = UserFacade::findByField('email',  $this->encode($email))->first();

        $userDataUpdate = array(
            'token' => $guid,
            'upd_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'upd_user' => $user->id
        );
        UserFacade::update($userDataUpdate, $user->id);
        ApplicationLogFacade::logActionMode2 ( LoggingAction::MODE2_UPDATE_USER, $user);
    }

    /**
     * Find user by email.
     *
     * @param email $email
     * @return user
     */
    function findUserByEmail($email)
    {
        $user = HelpLinkServiceFacade::findByField('email', $this->encode($email))->first();
        return $user;
    }

    /**
     * Find user by username.
     *
     * @param username $username
     * @return user
     */
    function findUserByUsername($username)
    {
        $user = UserFacade::findByField('username', $username)->first();
        return $user;
    }

    /**
     * Check the guid of user exists in database
     *
     * @param username $username
     * @param guid $guid
     * @return boolean
     */
    function checkGuid($username, $guid)
    {
        $user = UserFacade::findByField('username', $username)->first();
        // echo $username; exit;
        if ($user == null || $user->token != $guid || $user->token == '')
            return false;

        return true;
    }

    /**
     * Get only user without admin and guest
     *
     * @return Listing
     */
    public function getUserWithNormal()
    {
        return User::has('user_with_group')->get();
    }

    /**
     * Get Array Users.
     *
     * @param String $default
     *
     * @return array
     */
    function getArrayUsers($default = null, $user = null, $isDefault = true)
    {
        $default = empty($default) ? '' : $default;
        if ($isDefault)
            $array[$default] = trans('common.select_item_null');

        $users = UserFacade::all();

        if ($users != null) {
            foreach ($users as $user) {
                $array[$user->id] = $user->username;
            }
        }
        return $array;
    }

    /**
     * Get Array authorization group.
     *
     * @return array
     */
    function getAuthorizationGroups()
    {
        $groups = GroupFacade::all();
        $arrayGroups[''] = trans('common.user_registration_search_group_option_default');
        foreach ($groups as $group) {
            if ($group->id != session('user')->user_group_id) {
                $arrayGroups[$group->id] = $group->group_name;
            }
        }
        return $arrayGroups;
    }

    /**
     * Find user by keyword
     *
     * @see \Gis\Models\Services\UserServiceInterface::findUserByKeyword()
     */
    function findUserByKeyword($keyword)
    {
        $result = array();

        $users = UserFacade::getWithOutAdminAndGuest($keyword);
        if (! empty($users)) {
            foreach ($users as $user) {
                $result[$user->id] = $user->username . '-' . $user->user_code;
            }
        }
        return $result;
    }

    /**
     * Get List System Numbers for edit user
     *
     * @return array() $systemNumbers
     */
    public function getSystemNumbers()
    {
        return array(
            '' => trans('common.select_item_null'),
            11 => trans('common.user_registration_system_number_11'),
            12 => trans('common.user_registration_system_number_12'),
            13 => trans('common.user_registration_system_number_13')
        );
    }

    /**
     * FInd User by Id
     *
     * @param int $id
     * @return mixed
     *
     * @throws GisException
     */
    function findById($id)
    {
        $user = UserFacade::findByField('id', $id)->first();
        if ($user) {
            return $user;
        } else {
            throw new GisException(trans('common.authorization_user_not_exists'));
        }
    }

}