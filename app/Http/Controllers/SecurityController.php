<?php

namespace Gis\Http\Controllers;

use Gis\Models\Services\SecurityFacade;
use Gis\Http\Requests\AuthenticateRequest;
use Gis\Models\SystemCode;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends CoreController {
	/**
	 * Login action
	 * Allow user login to system through page view
	 *
	 * @return Ambigous <\Illuminate\View\View, mixed, \Illuminate\Foundation\Application, \Illuminate\Container\static>
	 */
	public function login() {
		if(SecurityFacade::isAuthenticate()){
			return redirect('/');
		}
        $validateData = array (
				'maxLengthPassword' => AuthenticateRequest::MAX_LENGTH_PASSWORD,
				'maxLengthUsername' => AuthenticateRequest::MAX_LENGTH_USERNAME 
		);
		//reset session id
		session_start();
		session_regenerate_id();
		return view ( 'admin.authentications.login', compact ( 'validateData' ) );
	}
	
	/**
	 * Login action with guest
	 * Allow guest login to system through link
	 *
	 * @return Ambigous <\Illuminate\View\View, mixed, \Illuminate\Foundation\Application, \Illuminate\Container\static>
	 */
	public function loginWithGuest() {
		$check = SecurityFacade::accessWithGuest ();
        if($check)
		    return redirect ( '' );
        else return view('errors.404');
	}
	
	/**
	 * Process request login from client
	 *
	 * @param Gis\Http\Requests\AuthenticateRequest $request        	
	 *
	 * @return Ambigous <\Illuminate\Routing\Redirector, \Illuminate\Http\RedirectResponse, mixed, \Illuminate\Foundation\Application, \Illuminate\Container\static>
	 */
	public function doLogin(AuthenticateRequest $request) {
		$dataLogin = $request->all ();
		SecurityFacade::authenticate ( $dataLogin );
		
		$user = session ( 'user' );
        $url = Session::get('attempted_url');
        Session::forget('attempted_url');
        if(strpos($url, 'users/confirm-change-account'))
            $redirect = $url;
        else if ($user->is_agreed)
			$redirect = url ( '' );
		else
			$redirect = url ( 'term' );
		
		$response = buildResponseMessage ( null, SystemCode::SUCCESS, $redirect );
		
		return response ()->json ( $response );
	}
	
	/**
	 * System process logout user
	 *
	 * @return Ambigous <\Illuminate\Routing\Redirector, \Illuminate\Http\RedirectResponse, mixed, \Illuminate\Foundation\Application, \Illuminate\Container\static>
	 */
	public function logout() {
		SecurityFacade::logout ();
		return redirect ( 'login' );
	}
}