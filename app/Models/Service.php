<?php

    namespace App\Models;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\Paginator;

    class Service extends Model
    {
        protected $table = 'services',
            $primaryKey = 'id',
            $fillable = [
            'parent_id',
            'title',
            'sub_title',
            'body',
            'background_fid',
            'alias_id',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'meta_robots',
            'sitemap',
            'style_id',
            'style_class',
            'language',
            'location',
            'status',
            'in_order_form',
            'relation_location_id',
            'access',
        ];
        public $default_theme = 'oleus.base.service';
        protected $perPage = 50;
        public $template;
        public $front_language;
        public $front_location;

        public function __construct()
        {
            $this->front_language = wrap()->get('locale', DEFAULT_LANGUAGE);
            $this->front_location = wrap()->get('location', DEFAULT_LOCATION);
        }

        public function scopeActive($query, $status = 1)
        {
            return $query->where('status', $status);
        }

        public function scopeLanguage($query, $language = null)
        {
            $language = $language ?? $this->front_language;

            return $query->where('language', $language);
        }

        public function scopeLocation($query, $location = null)
        {
            $location = $location ?? $this->front_location;

            return $query->whereNull('location')
                ->orWhere('location', $location)
                ->orderByDesc('location');
        }

        public function scopeInForm($query, $status = 1)
        {
            return $query->where('in_order_form', $status);
        }

        public function scopeAccess($query, $access = 1)
        {
            return $query->where('access', $access);
        }

        public function _alias()
        {
            return $this->hasOne(UrlAlias::class, 'id', 'alias_id');
        }

        public function _prices()
        {
            return $this->hasMany(ServicePrice::class, 'service_id')
                ->orderBy('sort');
        }

        public function _background()
        {
            return $this->hasOne(File::class, 'id', 'background_fid');
        }

        public function _background_asset($preset = NULL)
        {
            if($this->exists && $this->_background) return image_render($this->_background, $preset);

            return NULL;
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

        public function _last_modified()
        {
            return $this->updated_at->format('D, d M Y H:i:s \G\M\T');
        }

        public function _load()
        {
            $_templates_page = [
                "front.services.{$this->type}_{$this->id}",
                "front.services.{$this->type}",
                $this->default_theme
            ];
            $this->last_modified = $this->_last_modified();
            $this->template = choice_template($_templates_page);

            return $this;
        }

        public function _render()
        {
            $entity = $this->_load();
            wrap()->set('seo._title', ($entity->meta_title ? $entity->meta_title : $entity->title));
            wrap()->set('seo._keywords', $entity->meta_keywords);
            wrap()->set('seo._description', $entity->meta_description);
            wrap()->set('seo._robots', $entity->meta_robots);
            wrap()->set('seo._last_modified', $entity->last_modified);
            wrap()->set('page._title', $entity->title);
            wrap()->set('page._id', $entity->style_id);
            wrap()->set('page._class', $entity->style_class, TRUE);
            wrap()->set('page._background', ($entity->background_fid ? $entity->_background_asset() : NULL));
            wrap()->set('page._scripts', config('frontend.scripts'));
            wrap()->set('page._styles', config('frontend.styles'));
            wrap()->set('breadcrumb', breadcrumb_render($this));
            wrap()->render();

            return $entity;
        }

        public function _short_code($data = NULL, $object)
        {
            $_response = NULL;
            if(!is_null($data)) {
                switch($object) {
                    case 'medias':
                        $_template = choice_template([
                            "front.services.service_medias_{$this->entity_id}_{$this->id}",
                            "front.services.service_medias_{$this->entity_id}",
                            'oleus.base.entity_medias'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                    case 'files':
                        $_template = choice_template([
                            "front.services.service_files_{$this->entity_id}_{$this->id}",
                            "front.services.service_files_{$this->entity_id}",
                            'oleus.base.entity_files'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                }
            }

            return $_response;
        }
    }
