<?php

    namespace App\Http\Controllers;

    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Routing\Controller as BaseController;

    class Controller extends BaseController
    {
        use AuthorizesRequests;
        use DispatchesJobs;
        use ValidatesRequests;

        public function __construct()
        {
            $this->middleware(function ($request, $next) {
                wrap()->_load($request);

                return $next($request);
            });
        }

        public function __form()
        {
            $exists_relation = exists_relation();

            return (object)[
                'title'              => NULL,
                'route'              => NULL,
                'route_tag'          => NULL,
                'theme'              => 'oleus.base.forms.form',
                'relation'           => [
                    'count'       => (bool)$exists_relation->count,
                    'view_link'   => TRUE,
                    'view_status' => TRUE,
                ],
                'seo'                => FALSE,
                'location'           => (bool)$exists_relation->location,
                'translate'          => (bool)$exists_relation->language,
                'button_name'        => trans('forms.button_save'),
                'additional_buttons' => NULL,
                'permission'         => [
                    'read'   => NULL,
                    'create' => NULL,
                    'update' => NULL,
                    'delete' => NULL,
                ],
                'tabs'               => [
                ]
            ];
        }
    }
