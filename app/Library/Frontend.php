<?php

    namespace App\Library;

    use App\Models\UrlAlias;

    trait Frontend
    {
        public $template;
        public $morph;
        public $front_language;
        public $front_location;
        public $entity;

        public function __construct()
        {
            $this->front_language = wrap()->get('locale');
            $this->front_location = wrap()->get('location');
        }

        public function set_wrap($entity = NULL)
        {
            wrap()->set('page._class', ['frontend']);
            if(isset($entity['page._scripts']) && is_array($entity['page._scripts'])) {
                wrap()->set('page._scripts', array_merge($entity['page._scripts'], config('frontend.scripts')));
            } else {
                wrap()->set('page._scripts', config('frontend.scripts'));
            }
            if(isset($entity['page._styles']) && is_array($entity['page._styles'])) {
                wrap()->set('page._styles', array_merge(config('frontend.styles'), $entity['page._styles']));
            } else {
                wrap()->set('page._styles', config('frontend.styles'));
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
    }