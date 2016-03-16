<?php namespace Gis\Http\Middleware;

use Closure;
use Gis\Models\Services\SecurityFacade;
use Gis\Models\SystemCode;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Authenticate {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		
		
		if (!SecurityFacade::isAuthenticate())
		{
            Session::put('attempted_url', URL::current());
			if ($request->ajax())
			{
				$responseData = buildResponseMessage(trans('common.Unauthorized'),SystemCode::UNAUTHORIZED);
				return response()->json($responseData);
			}
			else
			{
				return redirect('login');
			}
		}

		return $next($request);
	}

}
