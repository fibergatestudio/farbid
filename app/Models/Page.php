<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Library\NovaPoshta;
    use Carbon\Carbon;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\Cache;

    class Page extends BaseModel
    {
        protected $table = 'pages';
        protected $guarded = [];
        protected $types;
        public $classIndex = 'page';

        public function __construct()
        {
            parent::__construct();
            $this->types = [
                'page_normal'            => trans('pages.pages_normal'),
                'page_list_nodes'        => trans('pages.pages_list_nodes'),
                'front'                  => trans('pages.pages_front'),
                'sitemap'                => trans('pages.pages_sitemap'),
                'search'                 => trans('pages.pages_search'),
                'contacts'               => trans('pages.pages_contacts'),
                'login'                  => trans('pages.pages_login'),
                'register'               => trans('pages.pages_register'),
                'reviews'                => trans('pages.pages_reviews'),
                'galleries'              => trans('pages.pages_galleries'),
                'discounts'              => trans('pages.pages_discounts'),
                'password_reset'         => trans('pages.pages_password_reset'),
                'error_403'              => trans('pages.pages_error_403'),
                'error_404'              => trans('pages.pages_error_404'),
                'error_500'              => trans('pages.pages_error_500'),
                'shop_basket'            => trans('pages.pages_shop_basket'),
                'shop_order_thanks_page' => trans('pages.pages_shop_order_thanks_page'),
            ];
        }

        public function _last_modified()
        {
            if($this->type != 'front') {
                $pages = $this->updated_at;
                $nodes = Node::where('entity_id', $this->id)
                    ->max('updated_at');
                if($pages && $nodes) {
                    $date_page = new Carbon($pages);
                    $date_nodes = new Carbon($nodes);
                    $date_diff = $date_page->diff($date_nodes);
                    if($date_diff->invert) {
                        return $date_page->format('D, d M Y H:i:s \G\M\T');
                    } else {
                        return $date_nodes->format('D, d M Y H:i:s \G\M\T');
                    }
                } elseif($pages) {
                    return Carbon::parse($pages)->format('D, d M Y H:i:s \G\M\T');
                } elseif($nodes) {
                    return Carbon::parse($nodes)->format('D, d M Y H:i:s \G\M\T');
                }
            }

            return Carbon::now()->format('D, d M Y H:i:s \G\M\T');
        }

        public function _types($type)
        {
            return $this->types[$type];
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
                $_response->background = [
                    'path'  => $entity->_background_asset(),
                    'style' => $entity->_background_style(),
                ];
                $_response->relation_entity = $_relation;

                return $_response;
            });
            $_templates = [
                "front.pages.{$this->type}_{$entity->relation_entity->id}",
                "front.pages.{$this->type}_{$entity->relation_entity->id}_page_{$this->id}",
                "front.pages.page_relation_{$entity->relation_entity->id}",
                "front.pages.page_relation_{$entity->relation_entity->id}_page_{$this->id}",
                "front.pages.page_{$this->id}",
                "front.pages.{$this->type}",
                "front.pages.page",
                "oleus.base.{$this->type}",
                "oleus.base.page_{$this->type}",
                'oleus.base.page'
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
                'page._background'   => $this->_background_style(),
                'breadcrumb'         => breadcrumb_render(['entity' => $this]),
                'alias'              => $this->_alias
            ]);
            switch($this->type) {
                case 'page_list_nodes':
                    $this->items = self::_items(9, TRUE);
                    break;
                case 'reviews':
                    $this->items = Review::_items();
                    break;
                case 'search':
                    $this->items = Search::query_search();
                    break;
                case 'shop_basket':
                    $this->items = ShopBasket::get_basket();
                    $_np = new NovaPoshta;
                    $this->np_area = $_np->get_area();
                    break;
                case 'sitemap':
                    $this->items = SiteMap::_tree();
                    break;
                case 'discounts':
                    $this->items = ShopProduct::discount_items($this);
                    break;
            }

            return $this;
        }

        public function _items($take = 'all', $paginate = FALSE, $exclude = [])
        {
            $items = Node::from('nodes as n')
                ->where('n.language', $this->relation_entity->language)
                ->where('n.location', $this->relation_entity->location)
                ->select('n.id')
                ->where('n.entity_id', $this->relation_entity->id)
                ->where('n.status', 1)
                ->when($exclude, function ($query) use ($exclude) {
                    return $query->whereNotIn('n.id', $exclude);
                })
                ->orderBy('n.published_at', 'desc')
                ->orderBy('n.sort')
                ->orderBy('n.updated_at', 'desc');
            if($paginate) {
                if($currentPage = currentPage()) {
                    Paginator::currentPageResolver(function () use ($currentPage) {
                        return $currentPage;
                    });
                }
                $items = $items->paginate($take);
                $_language = $this->language;
                if($items->isNotEmpty() && count($items->items())) {
                    $items->getCollection()->transform(function ($_node) use ($_language) {
                        return node_load($_node->id, $_language);
                    });
                }
            } elseif(is_numeric($take) && $take) {
                $items = $items->take($take)
                    ->get();
            } else {
                $items = $items->get();
            }
            if($paginate) {
                $_wrap = wrap()->get();
                $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
                $_current_page = $items->currentPage();
                $_next_page = $_current_page+1;
                $_prev_page = ($_prev = $_current_page-1) && $_prev > 0 ? $_prev : 1;
                $_query_string = NULL;
                $_next_page_link = NULL;
                $_prev_page_link = NULL;
                if($queryArray = request()->query()) {
                    unset($queryArray['page']);
                    if(count($queryArray)) {
                        foreach($queryArray as $query => $value) {
                            if($value) {
                                $_query_string[] = "{$query}={$value}";
                            }
                        }
                        $_query_string = $_query_string ? '?' . implode('&', $_query_string) : '';
                    }
                }
                if($_current_page < $items->lastPage()) {
                    $url = trim($_current_url, '/') . "/page-{$_next_page}";
                    $_next_page_link = _u($url) . $_query_string;
                }
                if($_current_page > 2) {
                    $url = trim($_current_url, '/') . "/page-{$_prev_page}";
                    $_prev_page_link = _u($url) . $_query_string;
                } else {
                    $url = trim($_current_url, '/');
                    $_prev_page_link = _u($url) . $_query_string;
                }
                wrap()->set('seo._link_prev', $_prev_page_link);
                wrap()->set('seo._link_next', $_next_page_link);
                wrap()->set('seo._page_number', $_current_page);
                if($_current_page > 1) {
                    wrap()->set('seo._robots', 'noindex, nofollow');
                    wrap()->set('seo._title_suffix', ' - ' . trans('others.page_full', ['page' => $_current_page]) . ' ' . wrap()->get('seo._title_suffix'));
                    wrap()->set('seo._description', ($this->meta_description ?? $_wrap['seo']['_description']) . ' - ' . trans('others.page_full', ['page' => $_current_page]));
                    wrap()->set('page._title', $this->title . ' - <i class="page-number">' . trans('others.page_full', ['page' => $_current_page]) . '</i>');
                    wrap()->set('breadcrumb', breadcrumb_render(['entity' => $this]), TRUE);
                }
            }

            return $items;
        }

        public function _render_ajax_command($entity = NULL)
        {
            $entity = $entity ? $entity : $this;
            $_wrap = wrap()->get();
            $commands = [];
            $_entity_alias = $entity->_alias;
            $_entity_alias = $_entity_alias->language != DEFAULT_LANGUAGE ? "{$_entity_alias->language}/{$_entity_alias->alias}" : $_entity_alias->alias;
            $_more_load = request()->get('more_load', 0);
            if($_more_load) {
                $commands[] = [
                    'target'  => '#pagination-page',
                    'command' => 'remove',
                ];
                $commands[] = [
                    'target'  => '.last-block-pagination',
                    'command' => 'remove',
                ];
            }
            if($entity->type == 'search') {
                $_items = Search::query_search();
                $commands[] = [
                    'command' => $_more_load ? 'append' : 'html',
                    'target'  => '#list-items-page',
                    'data'    => view("front.pages.search_items", [
                        '_more_load' => $_more_load,
                        'items'      => $_items,
                        'language'   => $_wrap['locale'],
                    ])
                        ->render()
                ];
            } elseif($entity->type == 'discounts') {
                $_items = ShopProduct::discount_items($entity);
                $commands[] = [
                    'command' => $_more_load ? 'append' : 'html',
                    'target'  => '.grid-category-product',
                    'data'    => view("front.shop.items_category_products", [
                        '_more_load' => $_more_load,
                        'items'      => $_items,
                        'language'   => $_wrap['locale'],
                    ])
                        ->render()
                ];
            } else {
                $_items = $entity->_items(9, TRUE);
                $commands[] = [
                    'command' => $_more_load ? 'append' : 'html',
                    'target'  => '#list-items-page > div',
                    'data'    => view("front.pages.page_list_nodes_{$entity->relation_entity->id}_items", [
                        '_more_load' => $_more_load,
                        'items'      => $_items,
                    ])
                        ->render()
                ];
            }
            $_current_page = $_items->currentPage();
            $_page_in_url = $_current_page && $_current_page > 1 ? "page-{$_current_page}/" : NULL;
            if($_current_page < 2 && $entity->body) {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#description-body-page',
                    'data'    => "<div class='page-body'>{$entity->body}</div>"
                ];
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#description-body-page',
                    'data'    => ''
                ];
            }
            $commands[] = [
                'command' => 'change_url',
                'url'     => _u($_entity_alias) . $_page_in_url . formalize_url_query()
            ];
            if($_current_page == 1) {
                $commands[] = [
                    'command' => 'change_title',
                    'title'   => "{$_wrap['seo']['_title']} {$_wrap['seo']['_title_suffix']}"
                ];
            } else {
                $commands[] = [
                    'command' => 'change_title',
                    'title'   => "{$_wrap['seo']['_title']} - " . trans('others.page_full', ['page' => $_current_page]) . " {$_wrap['seo']['_title_suffix']}"
                ];
            }
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '.uk-breadcrumb',
                'data'    => view('oleus.base.breadcrumb')->render()
            ];

            return $commands;
        }

        public function _last_nodes($take = 5, $exclude = [])
        {
            $_templates_page = [
                "front.nodes.last_nodes_{$this->type}_{$this->id}",
                "front.nodes.last_nodes_{$this->type}_{$this->relation}",
                "front.nodes.last_nodes_{$this->type}",
                "front.nodes.last_nodes_{$this->id}",
                "front.nodes.last_nodes_{$this->relation}",
                "front.nodes.last_nodes",
                "oleus.base.last_nodes_{$this->type}",
                "oleus.base.last_nodes_{$this->type}",
                'oleus.base.last_nodes'
            ];
            $this->items = $this->_items($take, FALSE, $exclude);
            $this->template = choice_template($_templates_page);

            return $this;
        }

        public function _short_code($data = NULL, $object)
        {
            $_response = NULL;
            if(!is_null($data) && (is_object($data) && $data->isNotEmpty())) {
                switch($object) {
                    case 'medias':
                        $_template = choice_template([
                            "front.pages.page_medias_{$this->type}_{$this->id}",
                            "front.pages.page_medias_{$this->type}",
                            "front.pages.page_medias_{$this->id}",
                            'front.pages.entity_medias',
                            'oleus.base.entity_medias'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                    case 'files':
                        $_template = choice_template([
                            "front.pages.page_files_{$this->type}_{$this->id}",
                            "front.pages.page_files_{$this->type}",
                            "front.pages.page_files_{$this->id}",
                            'front.pages.entity_files',
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
