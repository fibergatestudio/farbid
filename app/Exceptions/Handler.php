<?php

    namespace App\Exceptions;

    use Exception;
    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Auth\AuthenticationException;
    use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

    class Handler extends ExceptionHandler
    {
        protected $dontReport = [
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
        ];

        public function report(Exception $exception)
        {
            parent::report($exception);
        }

        public function render($request, Exception $exception)
        {
            //            if($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException){
            //                dd($exception);
            //                return $this->unauthorized($request, $exception);
            //            }
            if($exception instanceof AuthenticationException) return $this->unauthenticated($request, $exception);
            if($exception instanceof AuthorizationException) return $this->unauthorized($request, $exception);
            if($this->isHttpException($exception)) {
                wrap()->_load($request);
                $_status_code = $exception->getStatusCode();
                if($_status_code != 404) $_status_code = 500;
                if($item = page_render("error_{$_status_code}")) return response()->view("errors.{$_status_code}", compact('item'), $_status_code, $exception->getHeaders());

                return $this->renderHttpException($exception);
            }

            return parent::render($request, $exception);
        }

        protected function unauthenticated($request, AuthenticationException $exception)
        {
            if($request->expectsJson()) {
                return response()
                    ->json([
                        'error' => 'Unauthenticated.'
                    ], 401);
            }

            return redirect()
                ->guest('login');
        }

        protected function unauthorized($request, AuthorizationException $exception)
        {
            if($request->expectsJson()) {
                return response()
                    ->json([
                        'error' => $exception->getMessage()
                    ], 403);
            }

            return redirect()
                ->back()
                ->with('notice', [
                    'message' => trans('notice.user_access_is_denied'),
                    'status'  => 'danger'
                ]);
        }
    }
