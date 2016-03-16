<?php

namespace Gis\Http\Middleware;

use Closure;

class AgreeTerm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->ajax() && session('user') && !session('user')->is_agreed) {
            return redirect('term');
        }
        return $next($request);
    }
}
