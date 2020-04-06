<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class Menu extends BaseModel
    {

        protected $table = 'menus';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $classIndex = 'menu';
        public $cache_depends_on_language = TRUE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _parents_item()
        {
            return $this->hasMany(MenuItems::class, 'menu_id')
                ->whereNull('parent_id')
                ->orderBy('sort')
                ->orderBy('title')
                ->with([
                    '_sub_items'
                ]);
        }

        public function _items()
        {
            return $this->hasMany(MenuItems::class, 'menu_id')
                ->orderBy('sort')
                ->orderBy('title')
                ->with([
                    '_sub_items',
                    '__alias'
                ]);
        }

        public function _items_tree()
        {
            $items = $this->_items()
                ->active(1)
                ->whereNull('parent_id')
                ->orderBy('sort')
                ->remember(15)
                ->get();
            $_items = $this->_items_tree_render($items);

            return $_items ? $_items : NULL;
        }

        public function _items_tree_render($items, $parent = NULL)
        {
            if ($items) {
                if (is_null($parent)) {
                    $parents = [];
                    foreach ($items as $item) {
                        $_children = $item->_sub_items;
                        $_item_data = unserialize($item->data);
                        if ($_item_url = $item->_get_url_item()) {
                            $_status = TRUE;
                            if ($_item_url->entity && !$_item_url->entity->status) $_status = FALSE;
                            if ($_status) {
                                if (USE_MULTI_LANGUAGE) {
                                    if ($this->front_language != DEFAULT_LANGUAGE) {
                                        if (isset($_item_data['translate'][$this->front_language]) && $_item_data['translate'][$this->front_language]) {
                                            if ($_item_data['translate'][$this->front_language]['name']) {
                                                $item->title = $_item_data['translate'][$this->front_language]['name'];
                                            }
                                            if ($_item_data['translate'][$this->front_language]['sub_name']) {
                                                $item->sub_title = $_item_data['translate'][$this->front_language]['sub_name'];
                                            }
                                        }
                                    }
                                }
                                $_item_icon = ($_item_data['icon'] && $icon = f_get($_item_data['icon'])) ? image_render($icon) : NULL;
                                $parents[$item->id] = [
                                    'entity'    => $item,
                                    'list_item' => [
                                        'class' => [
                                            'uk-item',
                                            $_children->count() ? 'uk-parent menu-parent' : 'menu-item',
                                            $_item_data['item_class']
                                        ],
                                    ],
                                    'item'      => [
                                        'entity'      => $_item_url->entity,
                                        'icon'        => $_item_icon,
                                        'title'       => $item->title,
                                        'description' => $item->sub_title,
                                        'path'        => $_item_url->alias,
                                        'anchor'      => $item->anchor,
                                        'class'       => [
                                            $_item_data['class']
                                        ],
                                        'id'          => $_item_data['id'],
                                        'prefix'      => $_item_data['prefix'],
                                        'suffix'      => $_item_data['suffix'],
                                    ],
                                    'children'  => ($_children->count() ? $this->_items_tree_render($_children, $item->id) : NULL)
                                ];
                            }
                        }
                    }

                    return $parents;
                } else {
                    $children = [];
                    foreach ($items as $item) {
                        if ($item->status) {
                            $_children = $item->_sub_items;
                            $_item_data = unserialize($item->data);
                            if ($_item_url = $item->_get_url_item()) {
                                $_status = TRUE;
                                if ($_item_url->entity && !$_item_url->entity->status) $_status = FALSE;
                                if ($_status) {
                                    if (USE_MULTI_LANGUAGE) {
                                        if ($this->front_language != DEFAULT_LANGUAGE) {
                                            if (isset($_item_data['translate'][$this->front_language]) && $_item_data['translate'][$this->front_language]) {
                                                if ($_item_data['translate'][$this->front_language]['name']) {
                                                    $item->title = $_item_data['translate'][$this->front_language]['name'];
                                                }
                                                if ($_item_data['translate'][$this->front_language]['sub_name']) {
                                                    $item->sub_title = $_item_data['translate'][$this->front_language]['sub_name'];
                                                }
                                            }
                                        }
                                    }
                                    $_item_icon = ($_item_data['icon'] && $icon = f_get($_item_data['icon'])) ? image_render($icon) : NULL;
                                    $children[$item->id] = [
                                        'entity'    => $item,
                                        'list_item' => [
                                            'class' => [
                                                'uk-item',
                                                $_children->count() ? 'uk-parent menu-parent' : 'menu-item',
                                                $_item_data['item_class']
                                            ],
                                        ],
                                        'item'      => [
                                            'entity'      => $_item_url->entity,
                                            'icon'        => $_item_icon,
                                            'title'       => $item->title,
                                            'description' => $item->sub_title,
                                            'path'        => $_item_url->alias,
                                            'anchor'      => $item->anchor,
                                            'class'       => [
                                                $_item_data['class']
                                            ],
                                            'id'          => $_item_data['id'],
                                            'prefix'      => $_item_data['prefix'],
                                            'suffix'      => $_item_data['suffix'],
                                        ],
                                        'children'  => ($_children->count() ? $this->_items_tree_render($_children,
                                            $item->id) : NULL)
                                    ];
                                }
                            }
                        }
                    }

                    return $children;
                }
            }
        }

        public function _load()
        {
            $_templates = [
                "front.pages.menu_{$this->key}",
                "front.menus.menu_{$this->id}",
                "front.menus.menu",
                'oleus.base.menu'
            ];
            $this->template = $this->template ?? choice_template($_templates);
            $this->items = $this->_items_tree();

            return $this;
        }
    }
