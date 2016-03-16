<?php
namespace Gis\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Gis\Models\Services\UserServiceFacade;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Gis\Services\Logging\ApplicationLogFacade;

class TermOfUseController extends CoreController {
	
	/**
	 * display screen Term Of Use
	 * 
	 * @return Response
	 */
	public function index() {

        $isAgreed = UserServiceFacade::findUserById(session('user')->id);
        if($isAgreed->is_agreed) {
            return Redirect::to('/');
        }

        return view('admin/term/index');

	}
/**
	 * display screen Term Of Use for user
	 *
	 * @return Response
	 */
	public function showTerm() {
        return view('admin/term/termForUser');

	}

    /**
     * Save term of use.
     * @return mixed
     */
    public function submit()
    {
        $isAgreed = (Request::all()['is_agree'] == 1)? true : false;
        if (!$isAgreed) {
            UserServiceFacade::saveAgreed(session('user')->id, array('is_agreed' => $isAgreed, 'last_logout_time' => Carbon::now()));
            Session::forget('user');
        } else {
          $user = UserServiceFacade::saveAgreed(session('user')->id, array('is_agreed' => $isAgreed,'upd_user'=>session('user')->user_code,'upd_time'=>Carbon::now()));
           Session::put ( 'user', $user );
        }
        return response()->json(buildResponseMessage('Save done', 200));
    }

}
