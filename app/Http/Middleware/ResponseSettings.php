<?php

    namespace App\Http\Middleware;

    use Carbon\Carbon;
    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\View\View;

    class ResponseSettings
    {
        public function handle(Request $request, Closure $next)
        {
            $response = $next($request);
            if(config('os_seo.last_modified', FALSE) && !wrap()->get('dashboard')) {
                if($response instanceof Response and $response->getOriginalContent() instanceof View) {
                    if($_last_modified = wrap()->get('seo._last_modified')) {
                        $IfModifiedSince = FALSE;
                        if(isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) $IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
                        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
                        if($IfModifiedSince && $IfModifiedSince >= strtotime($_last_modified)) {
                            $response->setContent(NULL);
                            $response->setStatusCode(304);
                        } else {
                            $response->headers->set('Last-Modified', $_last_modified);
                            $response->headers->set('Expires', date("r", Carbon::now()->addMinute(15)->timestamp));
                        }
                    }
                }
            }

            return $response;
        }
    }
