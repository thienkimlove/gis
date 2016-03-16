<?php namespace Gis\Http\Middleware;

use Closure;
use Gis\Models\Services\SecurityFacade;
use Gis\Models\SystemCode;

class Permission {

	/**
	 * When user is login, check if user have permission to access page or not.     *
     * Rules for each page specific in routes.php	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
    public function handle($request, Closure $next)
    {
        if (SecurityFacade::isAuthenticate()) {
            $action = $request->route()->getAction();
            if (!empty($action['roles']) && !SecurityFacade::authorize($action['roles'])) {
                if ($request->ajax())
                {
                	$responseData = buildResponseMessage(trans('common.usergroup_not_authorized'),SystemCode::PERMISSION_DENIED);
                	return response()->json($responseData);
                }
                return redirect('server-error/403');
            }
        }
        
        return $next($request);
    }

}
