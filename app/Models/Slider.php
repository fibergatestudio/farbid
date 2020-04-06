<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class Slider extends BaseModel
    {
        protected $table = 'sliders';
        protected $guarded = [];
        public $classIndex = 'slider';
        public $cache_depends_on_language = TRUE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _items()
        {
            return $this->hasMany(SliderItems::class, 'slider_id')
                ->with([
                    '_background'
                ])
                ->orderBy('sort');
        }

        public function _load()
        {
            $_templates = [
                "front.sliders.slider_relation_{$this->relation}_slider_{$this->id}",
                "front.sliders.slider_relation_{$this->relation}",
                "front.sliders.slider_{$this->id}",
                "front.sliders.slider",
                'oleus.base.sliders'
            ];
            $this->template = $this->template ?? choice_template($_templates);
            $this->items = $this->_items;
            //            $_entity = $this;
            //            $this->items = Cache::rememberForever("{$this->classIndex}_{$this->id}_{$this->front_language}", function () use ($_entity) {
            //                return $_entity->_items;
            //            });

            return $this;
        }

        public function _short_code($data = NULL, $object = NULL)
        {
            $_response = NULL;

            return $_response;
        }

        public function setDuplicate($language = NULL, $location = NULL)
        {
            if ($language || $location) {
                $_exists = self::where('relation', $this->id);
                if ($location) {
                    $_exists->where('location', $location);
                } else {
                    $_exists->where('location', DEFAULT_LOCATION);
                }
                if ($language) {
                    $_exists->where('language', $language);
                } else {
                    $_exists->where('language', DEFAULT_LANGUAGE);
                }
                $_exists = $_exists->count();
                if ($_exists == 0) {
                    $_save = $this->toArray();
                    unset($_save['id']);
                    unset($_save['created_at']);
                    unset($_save['updated_at']);
                    $_save['title'] .= ' (copy)';
                    $_save['relation'] = $this->id;
                    if ($language) $_save['language'] = $language;
                    if ($location) $_save['location'] = $location;
                    if (isset($_save['background_fid']) && $_save['background_fid']) $_save['background_fid'] = File::duplicate($_save['background_fid']);
                    $item = self::updateOrCreate([
                        'id' => NULL
                    ], $_save);
                    $_slider_items = SliderItems::where('slider_id', $this->id)
                        ->get([
                            'title',
                            'sub_title',
                            'background_fid',
                            'body',
                            'sort',
                            'status',
                            'hidden_title',
                        ]);
                    if ($_slider_items->isNotEmpty()) {
                        $_slider_items = $_slider_items->map(function ($_item) use ($item) {
                            $_item['slider_id'] = $item->id;

                            return $_item;
                        });
                        SliderItems::insert($_slider_items->toArray());
                    }

                    return $item;
                }
            }

            return NULL;
        }
    }
