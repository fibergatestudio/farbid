<?php

    namespace App\Models;

    use App\User;
    use Illuminate\Database\Eloquent\Model;

    class Banner extends Model
    {
        protected $table = 'banners',
            $primaryKey = 'id',
            $fillable = [
            'user_id',
            'banner_fid',
            'alias_id',
            'link',
            'preset',
            'status',
            'access'
        ];
        protected $perPage = 50;
        public $default_theme = 'oleus.base.banner';

        public function scopeActive($query)
        {
            return $query->where('status', 1);
        }

        public function _banner()
        {
            return $this->hasOne(File::class, 'id', 'banner_fid');
        }

        public function _banner_asset($preset = NULL, $options = [])
        {
            if($this->exists && $this->_banner) {
                $_options = array_merge([
                    'no_last_modify' => FALSE,
                    'only_way'       => TRUE
                ], $options);

                return image_render($this->_banner, $preset, $_options);
            }

            return NULL;
        }

        public function _author()
        {
            return $this->hasOne(User::class, 'id', 'user_id');
        }

        public function _preset()
        {

            if($this->preset && ($_preset = config("os_images.{$this->preset}"))) {
                if(isset($_preset['w']) && isset($_preset['h'])) {
                    $_label = "{$_preset['w']}px * {$_preset['h']}px";
                } elseif(isset($_preset['w'])) {
                    $_label = "{$_preset['w']}px * auto";
                } elseif(isset($_preset['h'])) {
                    $_label = "auto * {$_preset['h']}px";
                }

                return $_label;
            }

            return NULL;
        }

        public function _load()
        {
            $this->theme = choice_template([
                "front.banners.banner_{$this->id}",
                $this->default_theme
            ]);
            $this->theme = $this->theme ? $this->theme : $this->default_theme;
            $this->banner = $this->banner_fid ? f_get($this->banner_fid, $this->preset) : NULL;

            return $this;
        }

        public function _get_alias()
        {
            if(!is_null($this->alias_id)) {
                $url_alias = UrlAlias::from('url_alias as a')
                    ->leftJoin('nodes as n', 'n.id', '=', 'a.model_id')
                    ->leftJoin('pages as p', 'p.id', '=', 'a.model_id')
                    ->leftJoin('services as s', 's.id', '=', 'a.model_id')
                    ->where('a.id', $this->alias_id)
                    ->first([
                        'a.model_type',
                        'a.alias as alias',
                        'n.title as node_title',
                        'p.title as page_title',
                        's.title as service_title',
                    ]);

                if($url_alias) {
                    switch($url_alias->model_type) {
                        case 'App\Models\Node':
                            return (object)[
                                'id'    => $this->alias_id,
                                'name'  => $url_alias->node_title,
                                'alias' => $url_alias->alias
                            ];
                            break;
                        case 'App\Models\Page':
                            return (object)[
                                'id'    => $this->alias_id,
                                'name'  => $url_alias->page_title,
                                'alias' => $url_alias->alias
                            ];
                            break;
                        case 'App\Models\Service':
                            return (object)[
                                'id'    => $this->alias_id,
                                'name'  => $url_alias->service_title,
                                'alias' => $url_alias->alias
                            ];
                            break;
                    }
                }
            } elseif($this->link) {
                return (object)[
                    'id'    => $this->link,
                    'name'  => $this->link,
                    'alias' => NULL
                ];
            }

            return NULL;
        }

        public function _short_code($data = NULL)
        {
            $_response = NULL;
            if(!is_null($data) && $data->status) {
                $_template = choice_template([
                    "front.banners.banner_{$data->id}",
                    'oleus.base.banner'
                ]);

                $_response = view($_template, ['item' => $data])
                    ->render();
            }

            return $_response;
        }
    }
