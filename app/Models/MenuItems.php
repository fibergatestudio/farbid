<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class MenuItems extends BaseModel
    {
        protected $table = 'menu_items';
        protected $guarded = [];
        public $timestamps = FALSE;
        private $defaultItems = [
            '<front>',
            '<none>'
        ];
        public $entity;

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

        public function _getAlias()
        {
            if (!is_null($this->alias_id)) {
                $url_alias = UrlAlias::from('url_alias as a')
                    ->leftJoin('nodes as n', 'n.id', '=', 'a.model_id')
                    ->leftJoin('pages as p', 'p.id', '=', 'a.model_id')
                    ->leftJoin('services as s', 's.id', '=', 'a.model_id')
                    ->leftJoin('shop_categories as sc', 'sc.id', '=', 'a.model_id')
                    ->where('a.id', $this->alias_id)
                    ->first([
                        'a.model_type',
                        'a.model_id',
                        'a.alias as alias',
                        'n.title as node_title',
                        'p.title as page_title',
                        's.title as service_title',
                        'sc.title as shop_category_title',
                    ]);

                if ($url_alias) {
                    switch ($url_alias->model_type) {
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
                        case 'App\Models\ShopCategory':
                            $_output = NULL;
                            $_category = ShopCategory::find($url_alias->model_id);
                            if ($_parents = $_category->parents) foreach ($_parents as $_parent) $_output[] = $_parent->title;
                            $_output[] = $_category->title;

                            return (object)[
                                'id'    => $this->alias_id,
                                'name'  => implode(' / ', $_output),
                                'alias' => $url_alias->alias
                            ];
                            break;
                    }
                }
            } elseif ($this->link) {
                return (object)[
                    'id'    => $this->link,
                    'name'  => $this->link,
                    'alias' => NULL
                ];
            }

            return NULL;
        }

        public function getParentsAttribute()
        {
            $_parents = NULL;
            if ($this->parent_id) $_parents = $this->_get_parent_menu_items($_parents, $this->parent_id);

            return $_parents ? array_reverse($_parents) : $_parents;
        }

        public function set($item)
        {
            if ($this->entity && is_array($item)) {
                $_item = is_numeric($item['id']) ? self::find($item['id']) : NULL;
                $entity_id = NULL;
                $link = NULL;
                if (in_array($item['link']['name'], $this->defaultItems)) {
                    $link = $item['link']['name'];
                } elseif ($item['link']['value']) {
                    $entity_id = $item['link']['value'];
                } else {
                    $link = $item['link']['name'];
                }
                if (!is_null($entity_id) || !is_null($link)) {
                    $request_icon = $item['data']['icon'] ? array_shift($item['data']['icon']) : NULL;
                    if ($request_icon) {
                        $item['data']['icon'] = $request_icon['id'];
                    }
                    $_d = self::updateOrCreate([
                        'id' => is_null($_item) ? NULL : $_item->id
                    ], [
                        'menu_id'   => $this->entity->id,
                        'title'     => $item['name'],
                        'sub_title' => $item['sub_name'],
                        'alias_id'  => $entity_id,
                        'link'      => $link,
                        'anchor'    => $item['anchor'],
                        'sort'      => isset($item['sort']) ? $item['sort'] : 0,
                        'parent_id' => isset($item['parent_id']) && $item['parent_id'] ? $item['parent_id'] : NULL,
                        'status'    => $item['status'],
                        'data'      => serialize($item['data']),
                    ]);
                    //                    dd($_d);
                }
            }
        }

        public function _children()
        {
            return $this->hasMany(self::class, 'parent_id', 'id')
                ->with([
                    '_sub_items',
                    '__alias'
                ])
                ->orderBy('sort');
        }

        public function __alias()
        {
            return $this->hasOne(UrlAlias::class, 'id', 'alias_id')
                ->with([
                    'model'
                ])
                ->leftJoin('nodes', 'nodes.id', '=', 'url_alias.model_id')
                ->leftJoin('pages', 'pages.id', '=', 'url_alias.model_id')
                ->leftJoin('shop_categories', 'shop_categories.id', '=', 'url_alias.model_id')
                ->select([
                    'url_alias.id',
                    'url_alias.model_id',
                    'url_alias.model_type',
                    'url_alias.language',
                    'url_alias.alias',
                    'nodes.id as node_id',
                    'nodes.status as node_status',
                    'pages.id as page_id',
                    'pages.status as page_status',
                    'shop_categories.id as shop_category_id',
                    'shop_categories.status as shop_category_status',
                ]);
        }

        public function _get_url_item()
        {
            $_language = $this->front_language;
            $_location = $this->front_location;
            if (is_null($this->alias_id) && $this->link) {
                if (in_array($this->link, $this->defaultItems)) {
                    if ($this->link == '<front>') {
                        return (object)[
                            'id'     => NULL,
                            'name'   => NULL,
                            'alias'  => _u('/', [], TRUE),
                            'entity' => NULL,
                            'status' => TRUE
                        ];
                    } elseif ($this->link == '<none>') {
                        return (object)[
                            'id'     => NULL,
                            'name'   => NULL,
                            'alias'  => NULL,
                            'entity' => NULL,
                            'status' => TRUE
                        ];
                    }
                } else {
                    return (object)[
                        'id'     => NULL,
                        'name'   => NULL,
                        'alias'  => $this->link,
                        'entity' => NULL,
                        'status' => TRUE
                    ];
                }
            } elseif ($this->alias_id) {
                //                $_alias_id = $this->alias_id;
                //                $_url_alias = UrlAlias::from('url_alias as a')
                //                    ->leftJoin('nodes as n', 'n.id', '=', 'a.model_id')
                //                    ->leftJoin('pages as p', 'p.id', '=', 'a.model_id')
                //                    ->leftJoin('shop_categories as sc', 'sc.id', '=', 'a.model_id')
                //                    ->where('a.id', $_alias_id)
                //                    ->first([
                //                        'a.id',
                //                        'a.model_type',
                //                        'n.id as node_id',
                //                        'n.status as node_status',
                //                        'p.id as page_id',
                //                        'p.status as page_status',
                //                        'sc.id as shop_category_id',
                //                        'sc.status as shop_category_status',
                //                    ]);
                $_url_alias = $this->__alias;
                if ($_url_alias) {
                    $_object = NULL;
                    if ($_url_alias->language != $_language) {
                        $_base_object = $_url_alias->model;
                        $_base_object_related = $_base_object->related;
                        if ($_base_object_related['items']) {
                            $_base_object_related['items']->each(function ($i) use (&$_base_object, $_language) {
                                if ($i->language == $_language) {
                                    $_base_object = $i;

                                    return FALSE;
                                }
                            });
                        }
                        $_url_alias = $_base_object->_alias;
                    }


                    //                    switch($_url_alias->model_type) {
                    //                        case 'App\Models\Node':
                    //                            $_object = node_load($_url_alias->node_id, $_language);
                    //                            $_alias = $_object->_alias;
                    //                            $_object_alias = $_alias->language != DEFAULT_LANGUAGE ? "{$_alias->language}/{$_alias->alias}" : $_alias->alias;
                    //
                    //                            return (object)[
                    //                                'id'     => $_alias->id,
                    //                                'name'   => $_object->title,
                    //                                'alias'  => $_object_alias,
                    //                                'entity' => $_object,
                    //                                'status' => $_object->status ? TRUE : FALSE
                    //                            ];
                    //                            break;
                    //                        case 'App\Models\Page':
                    //                            $_object = page_load($_url_alias->page_id, $_language);
                    //                            $_alias = $_object->_alias;
                    //                            $_object_alias = $_alias->language != DEFAULT_LANGUAGE ? "{$_alias->language}/{$_alias->alias}" : $_alias->alias;
                    //
                    //                            return (object)[
                    //                                'id'     => $_alias->id,
                    //                                'name'   => $_object->title,
                    //                                'alias'  => $_object_alias,
                    //                                'entity' => $_object,
                    //                                'status' => $_object->status ? TRUE : FALSE
                    //                            ];
                    //                            break;
                    //                        case 'App\Models\ShopCategory':
                    //                            $_object = shop_category_load($_url_alias->shop_category_id, $_language);
                    //                            $_alias = $_object->_alias;
                    //                            $_object_alias = $_alias->language != DEFAULT_LANGUAGE ? "{$_alias->language}/{$_alias->alias}" : $_alias->alias;
                    //
                    //                            return (object)[
                    //                                'id'     => $_alias->id,
                    //                                'name'   => $_object->title,
                    //                                'alias'  => $_object_alias,
                    //                                'entity' => $_object,
                    //                                'status' => $_object->status ? TRUE : FALSE
                    //                            ];
                    //                            break;
                    //                    }
                    //                    if($_object_id) {
                    ////                        $_alias_url = $_url_alias->language != DEFAULT_LANGUAGE ? "{$_url_alias->language}/{$_url_alias->alias}" : $_url_alias->alias;
                    ////                        $_object_alias = $_model::from("{$_model_table} as o")
                    ////                            ->leftJoin('url_alias as a', 'a.id', '=', 'o.alias_id')
                    ////                            ->where('o.relation', $_object_id)
                    ////                            //                            ->where('o.location', $_location)
                    ////                            ->where('o.language', $_language)
                    ////                            ->first([
                    ////                                'a.alias',
                    ////                                'a.language',
                    ////                                'a.id',
                    ////                                'o.title',
                    ////                            ]);
                    ////                        if($_object_alias) {
                    ////                            $_alias_id = $_object_alias->id;
                    ////                            $_alias_url = $_object_alias->language != DEFAULT_LANGUAGE ? "{$_object_alias->language}/{$_object_alias->alias}" : $_object_alias->alias;
                    ////                            $_object_title = $_object_alias->title;
                    ////                        }
                    //                        return (object)[
                    //                            'id'     => $_alias_id,
                    //                            'name'   => $_object_title,
                    //                            'alias'  => $_object_alias,
                    //                            'entity' => $_object,
                    //                        ];
                    //                    }

                    $_object = $_url_alias->model;
                    $_object_alias = $_url_alias->language != DEFAULT_LANGUAGE ? "{$_url_alias->language}/{$_url_alias->alias}" : $_url_alias->alias;

                    return (object)[
                        'id'     => $_url_alias->id,
                        'name'   => $_object->title,
                        'alias'  => $_object_alias,
                        'entity' => $_object,
                        'status' => $_object->status ? TRUE : FALSE
                    ];
                }
            }

            return NULL;
        }

        public function _sub_items()
        {
            return $this->hasMany('App\Models\MenuItems', 'parent_id', 'id')
                ->with([
                    '_sub_items',
                    '__alias'
                ])
                ->orderBy('sort');
            // $items = self::where('parent_id', $this->id)
            //  ->orderBy('sort')
            //     ->get();
            //
            // return $items ?? NULL;
        }

        public function _get_parent_menu_items(&$_data, $item_id)
        {
            $_menu_item = self::find($item_id);
            $_data[] = $_menu_item;
            if ($_menu_item->parent_id) $this->_get_parent_menu_items($_data, $_menu_item->parent_id);

            return $_data;
        }
    }
