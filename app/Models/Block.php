<?php

    namespace App\Models;

    use App\User;
    use Illuminate\Database\Eloquent\Model;

    class Block extends Model
    {
        protected $table = 'blocks',
            $primaryKey = 'id',
            $fillable = [
            'user_id',
            'language',
            'location',
            'background_fid',
            'title',
            'sub_title',
            'body',
            'style_id',
            'style_class',
            'status',
            'hidden_title',
            'sort',
            'access',
            'relation'
        ];
        protected $perPage = 50;
        public $default_theme = 'oleus.base.block';
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

        public function _author()
        {
            return $this->hasOne(User::class, 'id', 'user_id');
        }

        public function _medias($type = 'medias')
        {
            $this->{$type} = FilesReference::whereModelId($this->id)
                ->whereModelType($this->getMorphClass())
                ->whereType($type)
                ->pluck('relation_fid');

            return File::whereIn('id', $this->{$type})
                ->orderBy('sort')
                ->get();
        }

        public function _load()
        {
            $this->theme = choice_template([
                "front.blocks.block_medias_{$this->entity_id}_{$this->id}",
                "front.blocks.block_medias_{$this->entity_id}",
                "front.blocks.block_{$this->id}",
                $this->default_theme
            ]);
            $this->theme = $this->theme ? $this->theme : $this->default_theme;
            $this->background = $this->background_fid ? f_get($this->background_fid) : NULL;

            return $this;
        }

        public function _short_code($data = NULL, $object = NULL)
        {
            $_response = NULL;
            if(!is_null($data)) {
                if(is_null($object)) {
                    // todo: дописать вставку блока
                } else {
                    switch($object) {
                        case 'medias':
                            $_template = choice_template([
                                "front.blocks.block_medias_{$this->entity_id}_{$this->id}",
                                "front.blocks.block_medias_{$this->id}",
                                'oleus.base.entity_medias'
                            ]);

                            return view($_template, ['items' => $data])
                                ->render();
                            break;
                        case 'files':
                            $_template = choice_template([
                                "front.blocks.block_files_{$this->entity_id}_{$this->id}",
                                "front.blocks.block_files_{$this->entity_id}",
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
                        'user_id'        => $this->user_id,
                        'language'       => $language ? $language : $this->language,
                        'location'       => $location ? $location : $this->location,
                        'background_fid' => $this->background_fid,
                        'title'          => $this->title,
                        'sub_title'      => $this->sub_title,
                        'body'           => $this->body,
                        'style_id'       => $this->style_id,
                        'style_class'    => $this->style_class,
                        'status'         => $this->status,
                        'hidden_title'   => $this->hidden_title,
                        'sort'           => $this->sort,
                        'access'         => $this->access,
                        'relation'       => $this->id
                    ]);

                    return $item;
                }
            }

            return NULL;
        }
    }
