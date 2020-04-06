<?php

    namespace App\Library;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\UrlAlias;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;
    use Watson\Rememberable\Rememberable;

    abstract class BaseModel extends Model
    {
        use Frontend;
        use Rememberable;

        protected $perPage = 50;
        public $template;
        public $cache_depends_on_language = FALSE;
        public $classIndex;

        public function __construct()
        {
            $this->front_language = wrap()->get('locale', DEFAULT_LANGUAGE);
            $this->front_location = wrap()->get('location', DEFAULT_LOCATION);
        }

        // scope
        public function scopeRelated($query, $id = NULL)
        {
            return $query->where('id', $id)
                ->orWhere('relation', $id)
                ->orderByRaw("CASE WHEN (`relation` = '{$id}') THEN 0 WHEN (`id` = '{$id}') THEN 1 END");
        }

        public function scopeLanguage($query, $language = NULL)
        {
            if($language && $language != DEFAULT_LANGUAGE) {
                $_default_language = DEFAULT_LANGUAGE;

                return $query->where('language', $_default_language)
                    ->orWhere('language', $language)
                    ->orderByRaw("CASE WHEN (`language` = '{$language}') THEN 0 WHEN (`language` = '{$_default_language}') THEN 1 END");
            } else {
                return $query->where('language', DEFAULT_LANGUAGE);
            }
        }

        public function scopeLocation($query, $location = NULL)
        {
            if($location && $location != DEFAULT_LOCATION) {
                $_default_location = DEFAULT_LOCATION;

                return $query->where('location', $_default_location)
                    ->orWhere('location', $location)
                    ->orderByRaw("CASE WHEN (`location` = '{$location}') THEN 0 WHEN (`location` = '{$_default_location}') THEN 1 END");
            } else {
                return $query->where('location', DEFAULT_LOCATION);
            }
        }

        public function scopeActive($query, $status = 1)
        {
            return $query->where('status', $status);
        }

        public function scopeAccess($query, $access = 1)
        {
            return $query->where('access', $access);
        }

        public function scopeBlocked($query, $blocked = 1)
        {
            return $query->where('blocked', $blocked);
        }

        // attribute

        public function getLanguageNameAttribute()
        {
            $_languages = wrap()->get('variables.i18n.languages');
            if($this->language) return $_languages[$this->language]['full_name'];

            return trans('forms.value_all_available');
        }

        public function getLocationCityAttribute()
        {
            $_locations = wrap()->get('variables.contacts.cities');
            if($this->location) return $_locations[$this->location][$this->language]['city'];

            return trans('forms.value_all_available');
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
                ->with([
                    '_alias'
                ])
                ->get();

            return $_response;
        }

        public function hasAttribute($attribute)
        {
            return array_key_exists($attribute, $this->attributes);
        }

        // others
        public function _alias()
        {
            return $this->hasOne(UrlAlias::class, 'id', 'alias_id');
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

        public function _background_style($preset = NULL, $options = [])
        {
            if($this->exists && ($_background_style = $this->_background_asset($preset, $options))) {
                return "background-image: url('{$_background_style}')";
            }

            return NULL;
        }

        public function _preview()
        {
            return $this->hasOne(File::class, 'id', 'preview_fid');
        }

        public function _preview_asset($preset = NULL, $options = [])
        {
            if($this->exists && ($_preview = $this->_preview)) {
                $_options = array_merge([
                    'no_last_modify' => FALSE,
                    'only_way'       => TRUE,
                    'attributes'     => []
                ], $options);

                return image_render($_preview, $preset, $_options);
            }

            return NULL;
        }

        public function _icon()
        {
            return $this->hasOne(File::class, 'id', 'icon_fid');
        }

        public function _icon_asset($preset = NULL, $options = [])
        {
            if($this->exists && ($_icon = $this->_icon)) {
                $_options = array_merge([
                    'no_last_modify' => FALSE,
                    'only_way'       => TRUE,
                    'attributes'     => []
                ], $options);

                return image_render($_icon, $preset, $_options);
            }

            return NULL;
        }

        public function _last_modified()
        {
            if(isset($this->updated_at)) return $this->updated_at->format('D, d M Y H:i:s \G\M\T');

            return Carbon::now()->format('D, d M Y H:i:s \G\M\T');
        }

        public function _medias($type = 'medias')
        {
            $_files_reference = FilesReference::from('files_reference as fr')
                ->leftJoin('file_managed as fm', 'fm.id', '=', 'fr.relation_fid')
                ->where('fr.model_id', $this->id)
                ->where('fr.model_type', $this->getMorphClass())
                ->where('fr.type', $type)
                ->orderBy('fm.sort')
                ->get([
                    'fm.*'
                ]);
            $this->{$type} = $_files_reference;

            return $_files_reference;
        }

        public function _author()
        {
            if($this->hasAttribute('user_id')) {
                return $this->hasOne(User::class, 'id', 'user_id');
            }

            return NULL;
        }

        // cache
        public function forgetCache()
        {
            $_class_name = $this->classIndex;
            if($this->hasAttribute('relation')) {
                $_relation_objects = self::where('relation', ($this->relation ?? $this->id))
                    ->get();
                if($_relation_objects->isNotEmpty()) {
                    $_relation_objects->each(function ($_item_relation) use ($_class_name) {
                        Cache::forget("{$_class_name}_{$_item_relation->id}");
//                        $_item_relation->_load();
                    });
                }
            }
            if($this->hasAttribute('modification_id')) {
                $_modification_objects = self::where('modification_id', $this->modification_id)
                    ->where('id', '<>', $this->id)
                    ->get();
                if($_modification_objects->isNotEmpty()) {
                    $_modification_objects->each(function ($_item_modification) use ($_class_name) {
                        Cache::forget("{$_class_name}_{$_item_modification->id}");
//                        $_item_modification->_load();
                    });
                }
            }
            if($this->cache_depends_on_language) {
                $_languages = config('os_languages.languages');
                foreach($_languages as $_language_code => $_language_data) Cache::forget("{$_class_name}_{$this->id}_{$_language_code}");
            } else {
                Cache::forget("{$_class_name}_{$this->id}");
//                $this->_load();
            }

            return $_class_name;
        }

        // duplicate
        public function setDuplicate($language = NULL, $location = NULL)
        {
            if(($language || $location) && $this->hasAttribute('relation')) {
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
                    $_save = $this->toArray();
                    unset($_save['id']);
                    unset($_save['created_at']);
                    unset($_save['updated_at']);
                    $_save['title'] .= ' (copy)';
                    $_save['relation'] = $this->id;
                    if($language) $_save['language'] = $language;
                    if($location) $_save['location'] = $location;
                    if(isset($_save['background_fid']) && $_save['background_fid']) $_save['background_fid'] = File::duplicate($_save['background_fid']);
                    if(isset($_save['preview_fid']) && $_save['preview_fid']) $_save['preview_fid'] = File::duplicate($_save['preview_fid']);
                    if(isset($_save['alias_id']) && $_save['alias_id']) $_save['alias_id'] = NULL;
                    $item = self::updateOrCreate([
                        'id' => NULL
                    ], $_save);
                    if($this->_alias) {
                        $_alias = UrlAlias::updateOrCreate([
                            'id' => NULL,
                        ], [
                            'model_id'   => $item->id,
                            'model_type' => $item->getMorphClass(),
                            'alias'      => "{$this->_alias->alias}-{$this->id}",
                            'language'   => $item->language,
                            'location'   => $item->location,
                        ]);
                        $item->update(['alias_id' => $_alias->id]);
                    }
                    $_file_reference = FilesReference::where('model_type', $this->getMorphClass())
                        ->where('model_id', $this->id)
                        ->get([
                            'model_type',
                            'type',
                            'relation_fid'
                        ]);
                    if($_file_reference->isNotEmpty()) {
                        $_file_reference = $_file_reference->map(function ($_file) use ($item) {
                            $_file_duplicate = File::duplicate($_file['relation_fid']);
                            $_file['model_id'] = $item->id;
                            $_file['relation_fid'] = $_file_duplicate;

                            return $_file;
                        });
                        FilesReference::insert($_file_reference->toArray());
                    }

                    return $item;
                }
            }

            return NULL;
        }
    }