<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Variable extends Model
    {
        protected $table = 'variables';
        protected $guarded = [];
        public $timestamps = FALSE;

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

        public function getDataAttribute()
        {
            return unserialize($this->value);
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

        public function getPercentageOfRelationAttribute()
        {
            $_number = exists_relation();
            $_number_of_added = self::where('relation', $this->id)
                ->count();

            return $_number ? round(100 * $_number_of_added / $_number->count, 0) : $_number;
        }

        public function get($key, $language = DEFAULT_LANGUAGE, $location = DEFAULT_LOCATION)
        {
            if(is_numeric($key)) {
                if($language != DEFAULT_LANGUAGE) {
                    if($_variable_primary = self::where('id', $key)
                        ->first()) {
                        if($_variable_secondary = self::where('relation', $_variable_primary->id)
                            ->language($language)
                            ->location($location)
                            ->first()) {
                            $_data = unserialize($_variable_secondary->value);

                            return $_variable_secondary->do ? eval($_data) : $_data;
                        }
                        $_data = unserialize($_variable_primary->value);

                        return $_variable_primary->do ? eval($_data) : $_data;
                    }
                } else {
                    if($_variable = self::where('id', $key)
                        ->first()) {
                        $_data = unserialize($_variable->value);

                        return $_variable->do ? eval($_data) : $_data;
                    }
                }
            } elseif(is_string($key)) {
                if($language != DEFAULT_LANGUAGE) {
                    if($_variable_primary = self::where('key', $key)
                        ->first()) {
                        if($_variable_secondary = self::where('relation', $_variable_primary->id)
                            ->language($language)
                            ->location($location)
                            ->first()) {
                            $_data = unserialize($_variable_secondary->value);

                            return $_variable_secondary->do ? eval($_data) : $_data;
                        }
                        $_data = unserialize($_variable_primary->value);

                        return $_variable_primary->do ? eval($_data) : $_data;
                    }
                } else {
                    if($_variable = self::where('key', $key)
                        ->first()) {
                        $_data = unserialize($_variable->value);

                        return $_variable->do ? eval($_data) : $_data;
                    }
                }
            }

            return NULL;
        }

        public function _short_code($data)
        {
            if($data->value && !$data->do) return unserialize($data->value);

            return NULL;
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
                        'title'    => $this->title,
                        'value'    => $this->value,
                        'language' => $language ? $language : $this->language,
                        'location' => $location ? $location : $this->location,
                        'comment'  => $this->comment,
                        'do'       => $this->do,
                        'relation' => $this->id,
                    ]);

                    return $item;
                }
            }

            return NULL;
        }
    }