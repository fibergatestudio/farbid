<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use App\User;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;

    class Node extends BaseModel
    {
        protected $table = 'nodes';
        protected $guarded = [];
        protected $dates = [
            'created_at',
            'updated_at',
            'published_at'
        ];
        public $classIndex = 'node';

        public function __construct()
        {
            parent::__construct();
        }

        public function getTypeAttribute()
        {
            return Page::find($this->entity_id);
        }

        //        public function _related_page()
        //        {
        //            return $this->hasOne(Page::class, 'id', 'entity_id');
        //        }

        public static function _types()
        {
            $_pages = Page::where('type', 'page_list_nodes')
                ->language()
                ->orderBy('title')
                ->pluck('title', 'id');

            return $_pages ?? NULL;
        }

        public function _load()
        {
            $entity = clone $this;
            $entity = Cache::rememberForever("{$this->classIndex}_{$this->id}", function () use ($entity) {
                $_response = new \stdClass();
                $_relation = clone $entity;
                if($entity->relation) $_relation = self::find($entity->relation);
                $_response->last_modified = $entity->_last_modified();
                $_response->body = content_render($entity);
                $_response->teaser = content_render($entity, 'teaser');
                $_response->page = $entity->type;
                $_response->author = $entity->_author;
                $_response->preview = [
                    'teaser_path' => $entity->_preview_asset('thumb_blog'),
                    'teaser_img'  => $entity->_preview_asset('thumb_blog', ['only_way' => FALSE]),
                    'full_path'   => $entity->_preview_asset('full_blog'),
                    'full_img'    => $entity->_preview_asset('full_blog', ['only_way' => FALSE]),
                ];
                $_response->background = [
                    'path'  => $entity->_background_asset(),
                    'style' => $entity->_background_style(),
                ];
                $_response->relation_entity = $_relation;

                return $_response;
            });
            $_templates = [
                "front.nodes.node_type_{$this->entity_id}_relation_{$entity->relation_entity->id}",
                "front.nodes.node_type_{$this->entity_id}_relation_{$entity->relation_entity->id}_node_{$this->id}",
                "front.nodes.node_relation_{$entity->relation_entity->id}",
                "front.nodes.node_relation_{$entity->relation_entity->id}_node_{$this->id}",
                "front.nodes.node_{$this->id}",
                "front.nodes.node_type_{$this->entity_id}",
                "front.nodes.node",
                "oleus.base.node",
            ];
            $this->template = choice_template($_templates);
            foreach($entity as $_key => $_data) $this->{$_key} = $_data;
        }

        public function _render()
        {
            $this->_load();
            $this->set_wrap([
                'seo._title'         => $this->meta_title ?? $this->title,
                'seo._keywords'      => $this->meta_keywords,
                'seo._description'   => $this->meta_description,
                'seo._robots'        => $this->meta_robots,
                'seo._last_modified' => $this->last_modified,
                'page._title'        => $this->title,
                'page._id'           => $this->style_id,
                'page._class'        => $this->style_class,
                'page._background'   => $this->background['style'],
                'breadcrumb'         => breadcrumb_render(['entity' => $this]),
                'alias'              => $this->_alias
            ]);

            return $this;
        }

        public function _short_code($data = NULL, $object)
        {
            $_response = NULL;
            if(!is_null($data) && (is_object($data) && $data->isNotEmpty())) {
                switch($object) {
                    case 'medias':
                        $_template = choice_template([
                            "front.nodes.node_entity_medias_{$this->entity_id}_{$this->id}",
                            "front.nodes.node_entity_medias_type_{$this->entity_id}",
                            'front.nodes.entity_medias',
                            'oleus.base.entity_medias'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                    case 'files':
                        $_template = choice_template([
                            "front.nodes.node_entity_files_{$this->entity_id}_{$this->id}",
                            "front.nodes.node_entity_files_type_{$this->entity_id}",
                            'front.nodes.entity_files',
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
