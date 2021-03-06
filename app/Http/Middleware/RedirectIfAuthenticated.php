<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\Auth;

    class RedirectIfAuthenticated
    {
        /**
         * Handle an incoming request.
         * @param  \Illuminate\Http\Request $request
         * @param  \Closure                 $next
         * @param  string|null              $guard
         * @return mixed
         */
        public function handle($request, Closure $next, $guard = NULL)
        {
            if(Auth::guard($guard)->check()) {
                if($request->user()->can('access_dashboard')) {
                    return redirect('oleus');
                } else {
                    return redirect('/');
                }
            }

            return $next($request);
        }
    }
