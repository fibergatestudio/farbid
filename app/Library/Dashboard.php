<?php

    namespace App\Library;

    trait Dashboard
    {
        protected $perPage = 1;

        public function set_wrap($entity = NULL)
        {
            wrap()->set('dashboard', TRUE);
            wrap()->set('seo._title', 'Dashboard');
            wrap()->set('page._class', ['uk-background-muted2']);
            wrap()->set('page._js_settings', [
                'translate' => [
                    'item_list_is_empty'    => trans('others.item_list_is_empty'),
                    'no_search_results'     => trans('others.no_search_results'),
                    'first'                 => trans('others.first'),
                    'last'                  => trans('others.last'),
                    'loading'               => trans('others.loading'),
                    'start_typing'          => trans('others.start_typing'),
                    'limit_max_item'        => trans('others.limit_max_item'),
                    'press_enter_to_add'    => trans('others.press_enter_to_add'),
                    'value_is_not_selected' => trans('others.value_is_not_selected'),
                    'upload_file_mime_type' => trans('notice.field_upload_mime_type'),
                    'yes'                   => trans('others.yes'),
                    'no'                    => trans('others.no'),
                    'confirm_file_delete'   => trans('notice.confirm_file_delete'),
                    'confirm_delete'        => trans('notice.confirm_delete'),
                    'validation'            => []
                ]
            ]);
            if(isset($entity['page._scripts']) && is_array($entity['page._scripts'])) {
                wrap()->set('page._scripts', array_merge(config('os_common.scripts'), $entity['page._scripts']));
            } else {
                wrap()->set('page._scripts', config('os_common.scripts'));
            }
            if(isset($entity['page._styles']) && is_array($entity['page._styles'])) {
                wrap()->set('page._styles', array_merge(config('os_common.styles'), $entity['page._styles']));
            } else {
                wrap()->set('page._styles', config('os_common.styles'));
            }
            if(is_array($entity) && $entity) {
                foreach($entity as $key => $value) {
                    if(!in_array($key, [
                        'page._scripts',
                        'page._styles'
                    ])) {
                        if($value) wrap()->set($key, $value);
                    }
                }
            }
            wrap()->render();
        }

        //        public function render_items($data = [])
        //        {
        //            return (object)array_merge([
        //                'buttons'        => [],
        //                'headers'        => [],
        //                'items'          => collect([]),
        //                'filteredFields' => NULL,
        //                'pagination'     => [
        //                    'total'       => NULL,
        //                    'currentPage' => NULL,
        //                    'lastPage'    => NULL,
        //                    'to'          => NULL,
        //                    'perPage'     => NULL
        //                ],
        //                'apiPath'        => NULL
        //            ], $data);
        //        }
    }