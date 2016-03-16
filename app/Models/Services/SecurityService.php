<?php

namespace Gis\Models\Services;

use Gis\Models\Repositories\FolderFacade;
use Gis\Models\Repositories\GroupFacade;
use Gis\Models\Repositories\HelpLinkRepositoryFacade;
use Gis\Models\Repositories\UserFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Gis\Models\SystemCode;
use Gis\Exceptions\GisException;
use Gis\Models\Entities\User;
use Gis\Services\Logging\ApplicationLogFacade;
use Gis\Helpers\LoggingAction;

/**
 * Service contain business logic, working between repositories and controllers.
 */
class SecurityService extends BaseService implements SecurityServiceInterface {
	/**
	 * Max Limit Login Count
	 *
	 * @var LOGIN_LIMIT_TIME
	 */
	const LOGIN_LIMIT_TIME = 4;
	
	/**
	 * Flag Encrypt users'password
	 *
	 * @var ENABLE_ENCRYPT_PASSWORD
	 */
	const ENABLE_ENCRYPT_PASSWORD = true;

	/**
	 * Default account guset
	 *
	 * @var GUEST_ACCOUNT
	 */
	const GUEST_ACCOUNT = 'guest';
	
	/**
	 * Process business login to system
	 *
	 * @see \Gis\Models\Services\SecurityServiceInterface::authenticate()
	 *
	 * @return Gis\Models\Entities\User
	 */
	function authenticate($request) {
		if (self::ENABLE_ENCRYPT_PASSWORD) {
			$passwordEncrypt = encryptString ( $request ['password'] );
			if ($passwordEncrypt)
				$request ['password'] = $passwordEncrypt;
		}
		
		if ($user = UserFacade::checkLogin ( $request )) {
			$this->filterUserActive ( $user );
            $user->email=$this->decode($user->email);
			if ($this->isAuthenticate ())
				Session::forget ( 'user' );
			Session::put ( 'user', $user );
			
			$dataResetLimitCount ['login_failed_count'] = 0;
			UserFacade::update ( $dataResetLimitCount, $user->id );

			ApplicationLogFacade::logAction ( LoggingAction::MODE1_USER_LOGIN,$user );
			return true;
		}
		
		$user = UserServiceFacade::findUserByUsername ( $request ['username'] );
		if ($user) {
			if ($user->user_locked_flg)
				throw new GisException ( trans ( 'common.login_user_is_locked' ), \Gis\Models\SystemCode::RESOURCE_LOCKED );
		}
		if (! empty ( $request ['username'] ))
			$this->isLockUser ( $request ['username'] );
		
		throw new GisException ( trans ( 'common.login_user_not_found' ), SystemCode::NOT_FOUND );
		
		return false;
	}
	
	/**
	 * Process logout user
	 *
	 * @see \Gis\Models\Services\SecurityServiceInterface::logout()
	 */
	function logout() {
		if (Session::has ( 'user' )) {
			$attributes ['last_logout_time'] = date ( 'Y-m-d H:i:s' );
			UserFacade::update ( $attributes, session ( 'user' )->id );
            if(session('user')->usergroup->is_guest_group){
                session_start();
                $arr=array();
                $guestMaps=FolderFacade::selectModel()->where('session_id',session_id())->get();
                foreach($guestMaps as $guestMap){
                    array_push($arr,$guestMap->id);
                }
                $data['folderIds']=$arr;
                FolderServiceFacade::deleteLayers($data);
            }
			ApplicationLogFacade::logAction ( LoggingAction::MODE1_USER_LOGOUT,session ( 'user' ) );
			Session::forget ( 'user' );
		}
	}
	
	/**
	 * Check permission user
	 *
	 * @param string $permission        	
	 *
	 * @see \Gis\Models\Services\SecurityServiceInterface::authorize()
	 *
	 * @return bool
	 */
	function authorize($permission)
    {
		if (Session::has ( 'user' )) {
			return GroupFacade::getById ( Session::get ( 'user' )->user_group_id )->$permission;
		}
		return false;
	}

    protected function _setUp()
    {
        $havePermissions = [];

        $currentPermissions = null;
        if (Session::has('user')) {
            $currentPermissions = GroupFacade::getById (Session::get('user')->user_group_id);
        }

        if (!empty($currentPermissions)) {
            foreach (UserServiceFacade::groupPermission() as $permission) {
                if ($currentPermissions->$permission) {
                    $havePermissions[$permission] = true;
                }
            }
        }
        return $havePermissions;
    }

    public function compose(View $view)
    {
        $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $helpLink=$this->findHelpLinkByAdd($url);
//        dd($helpLink);
        $view->with([
            'menuPermissions'=> $this->_setUp(),
            'helpLink'=> $helpLink
        ]);
    }
    function findHelpLinkByAdd($url)
    {
        if($url = HelpLinkRepositoryFacade::findByField('address', $url)->first())
        return $url;
        else return 0;
    }
	
	/**
	 * Check if current user is authenticate or not.
	 *
	 * @return mixed
	 */
	function isAuthenticate() {
		return Session::has ( 'user' );
	}
	
	/**
	 * Lock user when pass limit login time
	 *
	 * @param string $username        	
	 *
	 * @see \Gis\Models\Services\UserServiceInterface::isLockUser()
	 *
	 */
	function isLockUser($username) {
		$user = UserFacade::findByField ( 'username', $username )->first ();
		$isLock = false;
		if ($user) {
			$currentLimitCount = $user->login_failed_count + 1;
			$userDataUpdate = array (
					'login_failed_count' => $currentLimitCount 
			);
			if ($currentLimitCount > self::LOGIN_LIMIT_TIME) {
				$userDataUpdate ['user_locked_flg'] = true;
				
				$isLock = true;
			}
			
			UserFacade::update ( $userDataUpdate, $user->id );
		}
		
		if ($isLock)
			throw new GisException ( trans ( 'common.login_user_is_locked' ), \Gis\Models\SystemCode::RESOURCE_LOCKED );
		
		return;
	}
	
	/**
	 * filter User Active
	 * with out lock status
	 *
	 * @param
	 *        	\Models\Entities\User
	 * @see \Gis\Models\Services\UserServiceInterface::loadUserByUsername()
	 *
	 * @return boolean
	 */
	function filterUserActive(User $user) {
		if ($user->user_locked_flg === true)
			throw new GisException ( trans ( 'common.login_user_is_locked'), \Gis\Models\SystemCode::RESOURCE_LOCKED );
	}
	
	/**
	 * Process login for guest user
	 *
	 * @see \Gis\Models\Services\SecurityServiceInterface::accessWithGuest()
	 *
	 * @return boolean
	 */
	function accessWithGuest() {
		$guest = UserFacade::getAccountGuest()->first();
		if (!$guest)
            return false;
        $user = UserServiceFacade::saveAgreed($guest->id, array('is_agreed' => false));
		Session::put ( 'user', $user );
		ApplicationLogFacade::logAction ( LoggingAction::MODE1_GUEST_LOGIN,$guest );
		return true;
	}
}
