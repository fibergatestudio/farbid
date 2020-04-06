<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Advantage extends Model
    {
        protected $table = 'advantages',
            $primaryKey = 'id',
            $fillable = [
            'location',
            'language',
            'position',
            'title',
            'sub_title',
            'background_fid',
            'style_id',
            'style_class',
            'body',
            'status',
            'hidden_title',
            'sort',
            'access',
            'relation'
        ];
        protected $perPage = 50;
        public $front_language;
        public $front_location;

        public function __construct()
        {
            $this->front_language = wrap()->get('locale', DEFAULT_LANGUAGE);
            $this->front_location = wrap()->get('location', DEFAULT_LOCATION);
        }

        public function scopeActive($query)
        {
            return $query->where('status', 1);
        }

        public function scopeAccess($query, $access = 1)
        {
            return $query->where('access', $access);
        }

        public function scopeLanguage($query, $language = NULL)
        {
            $language = $language ?? $this->front_language;

            return $query->where('language', $language);
        }

        public function scopeLocation($query, $location = NULL)
        {
            $location = $location ?? $this->front_location;

            return $query->where('location', DEFAULT_LOCATION)
                ->orWhere('location', $location)
                ->orderByDesc('location');
        }

        public function getPercentageOfRelationAttribute()
        {
            $_number = exists_relation();
            $_number_of_added = self::where('relation', $this->id)
                ->count();

            return $_number ? round(100 * $_number_of_added / $_number->count, 0) : $_number;
        }

        public function getRelatedAttribute()
        {
            $_response = [
                'this'    => $this,
                'primary' => NULL,
                'items'   => NULL,
            ];
            if(is_null($this->relation)) {
                $_response['primary'] = $this;
                $_response['this'] = TRUE;
            } else {
                $_response['primary'] = self::find($this->relation);
            }
            $_response['items'] = self::where('relation', $_response['primary']->id)
                ->orderBy('location')
                ->orderBy('language')
                ->get();

            return $_response;
        }

        public function getLocationCityAttribute()
        {
            $_locations = config('os_contacts.cities');
            if($this->location) return $_locations[$this->location][DEFAULT_LANGUAGE]['city'];

            return trans('forms.value_all_available');
        }

        public function getLanguageNameAttribute()
        {
            $_languages = config('os_languages.languages');
            if($this->language) return $_languages[$this->language]['full_name'];

            return trans('forms.value_all_available');
        }

        public function _background()
        {
            return $this->hasOne(File::class, 'id', 'background_fid');
        }

        public function _background_asset($preset = NULL, $options = [])
        {
            if($this->exists && $this->_background) {
                $_options = array_merge([
                    'no_last_modify' => FALSE,
                    'only_way'       => TRUE
                ], $options);

                return image_render($this->_background, $preset, $_options);
            }

            return NULL;
        }

        public function _items()
        {
            return $this->hasMany(AdvantageItems::class, 'advantage_id')
                ->orderBy('sort');
        }

        public function _load()
        {
            $_templates_page = [
                "front.advantages.advantage_{$this->id}",
                'oleus.base.advantage'
            ];
            $this->theme = $this->theme ? $this->theme : choice_template($_templates_page);
            $this->background = $this->background_fid ? f_get($this->background_fid) : NULL;

            return $this;
        }

        public function _short_code($data = NULL, $object = NULL)
        {
            $_response = NULL;
            if(!is_null($data)) {
                if(is_null($object)) {
                    // todo: дописать вставку перимуществ
                } else {
                    switch($object) {
                        case 'medias':
                            $_template = choice_template([
                                "front.advantages.advantage_medias_{$this->entity_id}_{$this->id}",
                                "front.advantages.advantage_medias_{$this->entity_id}",
                                'oleus.base.entity_medias'
                            ]);

                            return view($_template, ['items' => $data])
                                ->render();
                            break;
                        case 'files':
                            $_template = choice_template([
                                "front.advantages.advantage_files_{$this->entity_id}_{$this->id}",
                                "front.advantages.advantage_files_{$this->entity_id}",
                                'oleus.base.entity_files'
                            ]);

                            return view($_template, ['items' => $data])
                                ->render();
                            break;
                    }
                }
            }

            return $_response;
        }

        public function _set_duplicate($language = NULL, $location = NULL)
        {
            if($language || $location) {
                $_exists = self::where('relation', $this->id);
                if($location) {
                    $_exists->where('location', $location);
                } else {
                    $_exists->where('location', DEFAULT_LOCATION);
                }
                if($language) {
                    $_exists->where('language', $language);
                } else {
                    $_exists->where('language', DEFAULT_LANGUAGE);
                }
                $_exists = $_exists->count();
                if($_exists == 0) {
                    $item = self::updateOrCreate([
                        'id' => NULL
                    ], [
                        'language'       => $language ? $language : $this->language,
                        'location'       => $location ? $location : $this->location,
                        'position'       => $this->position,
                        'title'          => $this->title,
                        'sub_title'      => $this->sub_title,
                        'background_fid' => $this->background_fid,
                        'style_id'       => $this->style_id,
                        'style_class'    => $this->style_class,
                        'body'           => $this->body,
                        'status'         => $this->status,
                        'hidden_title'   => $this->hidden_title,
                        'sort'           => $this->sort,
                        'access'         => $this->access,
                        'relation'       => $this->id
                    ]);
                    $_advantage_items = AdvantageItems::where('advantage_id', $this->id)
                        ->get([
                            'title',
                            'sub_title',
                            'icon_fid',
                            'body',
                            'sort',
                            'status',
                            'hidden_title',
                        ]);
                    if($_advantage_items->isNotEmpty()) {
                        $_advantage_items = $_advantage_items->map(function ($_item) use ($item) {
                            $_item['advantage_id'] = $item->id;

                            return $_item;
                        });
                        AdvantageItems::insert($_advantage_items->toArray());
                    }

                    return $item;
                }
            }

            return NULL;
        }
    }
