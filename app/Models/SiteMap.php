<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\Cache;

    class SiteMap extends BaseModel
    {
        public function __construct(array $attributes = [])
        {
            parent::__construct($attributes);
        }

        public static function _tree()
        {
            $_language = wrap()->get('locale');
            $_items = Cache::remember("sitemap_{$_language}", 86400, function () use ($_language) {
                $_items = collect([]);
                if ($_page_front = Page::where('type', 'front')
                    ->language($_language)
                    ->location()
                    ->active()
                    ->orderByDesc('location')
                    ->first()) {
                    $_items->push([
                        'name'          => $_page_front->title,
                        'url'           => $_page_front->location ? "/{$_page_front->location}/" : '/',
                        'last_modified' => $_page_front->_last_modified(),
                        'items'         => NULL
                    ]);
                }
                $_url_items = UrlAlias::where('model_type', 'App\Models\Page')
                    ->where('language', $_language)
                    ->get();
                if ($_url_items->isNotEmpty()) {
                    $_url_items->map(function ($_url) use (&$_items, $_language) {
                        if ($_page = Page::find($_url->model_id)) {
                            if ($_page->sitemap && $_page->status) {
                                $_nodes = NULL;
                                if ($_page->type == 'page_list_nodes') {
                                    $_nodes_items = Node::where('entity_id', $_page->id)
                                        ->language($_language)
                                        ->location()
                                        ->active()
                                        ->orderBy('sort')
                                        ->get();
                                    if ($_nodes_items->isNotEmpty()) {
                                        $_nodes = collect([]);
                                        $_nodes_items->map(function ($_node) use (&$_nodes) {
                                            if ($_node->sitemap && $_node->status) {
                                                $_nodes->push([
                                                    'name'          => $_node->title,
                                                    'url'           => $_node->_alias->language != DEFAULT_LANGUAGE ? _u("{$_node->_alias->language}/{$_node->_alias->alias}") : _u($_node->_alias->alias),
                                                    'last_modified' => $_node->_last_modified(),
                                                    'items'         => NULL
                                                ]);
                                            }
                                        });
                                    }
                                }
                                $_items->push([
                                    'name'          => $_page->title,
                                    'url'           => $_url->language != DEFAULT_LANGUAGE ? _u("{$_url->language}/{$_url->alias}") : _u($_url->alias),
                                    'last_modified' => $_page->_last_modified(),
                                    'items'         => $_nodes
                                ]);
                            }
                        }
                    });
                }

                $_shop_category = ShopCategory::whereNull('parent_id')
                    ->language($_language)
                    ->active()
                    ->where('sitemap', 1)
                    ->orderBy('sort')
                    ->orderBy('title')
                    ->get();
                if ($_shop_category->isNotEmpty()) {
                    $_shop_category->map(function ($_category) use (&$_items, $_language) {
                        $_push_items = collect([]);
                        $_sub_category = $_category->children;
                        if ($_sub_category->isNotEmpty()) {
                            $_sub_category->map(function ($_sub_category) use (&$_push_items, $_language) {
                                $_push_items_2 = collect([]);
                                $_sub_category_2 = $_sub_category->children;
                                if ($_sub_category_2->isNotEmpty()) {
                                    $_sub_category_2->map(function ($_sub_category_3) use (&$_push_items_2) {
                                        $_push_items_3 = collect([]);
                                        $_products_items = ShopProduct::from('shop_products as p')
                                            ->leftJoin('shop_product_categories as c', 'c.product_id', '=', 'p.id')
                                            ->where('c.category_id', $_sub_category_3->id)
                                            ->where('p.status', 1)
                                            ->where('p.sitemap', 1)
                                            ->get([
                                                'p.*'
                                            ]);
                                        if ($_products_items->isNotEmpty()) {
                                            $_products_items->map(function ($_product) use (&$_push_items_3) {
                                                $_push_items_3->push([
                                                    'name'          => $_product->title,
                                                    'url'           => $_product->_alias->language != DEFAULT_LANGUAGE ? _u("{$_product->_alias->language}/{$_product->_alias->alias}") : _u($_product->_alias->alias),
                                                    'last_modified' => $_product->_last_modified(),
                                                    'items'         => NULL
                                                ]);
                                            });
                                        }
                                        $_push_items_2->push([
                                            'name'          => $_sub_category_3->title,
                                            'url'           => $_sub_category_3->_alias->language != DEFAULT_LANGUAGE ? _u("{$_sub_category_3->_alias->language}/{$_sub_category_3->_alias->alias}") : _u($_sub_category_3->_alias->alias),
                                            'last_modified' => $_sub_category_3->_last_modified(),
                                            'items'         => $_push_items_3
                                        ]);
                                    });
                                }
                                $_products_items = ShopProduct::from('shop_products as p')
                                    ->leftJoin('shop_product_categories as c', 'c.product_id', '=', 'p.id')
                                    ->where('c.category_id', $_sub_category->id)
                                    ->where('p.status', 1)
                                    ->where('p.sitemap', 1)
                                    ->get([
                                        'p.*'
                                    ]);
                                if ($_products_items->isNotEmpty()) {
                                    $_products_items->map(function ($_product) use (&$_push_items_2, $_language) {
                                        $_push_items_2->push([
                                            'name'          => $_product->title,
                                            'url'           => $_product->_alias->language != DEFAULT_LANGUAGE ? _u("{$_product->_alias->language}/{$_product->_alias->alias}") : _u($_product->_alias->alias),
                                            'last_modified' => $_product->_last_modified(),
                                            'items'         => NULL
                                        ]);
                                    });
                                }
                                $_push_items->push([
                                    'name'          => $_sub_category->title,
                                    'url'           => $_sub_category->_alias->language != DEFAULT_LANGUAGE ? _u("{$_sub_category->_alias->language}/{$_sub_category->_alias->alias}") : _u($_sub_category->_alias->alias),
                                    'last_modified' => $_sub_category->_last_modified(),
                                    'items'         => $_push_items_2
                                ]);
                            });
                        }
                        $_products_items = ShopProduct::from('shop_products as p')
                            ->leftJoin('shop_product_categories as c', 'c.product_id', '=', 'p.id')
                            ->where('c.category_id', $_category->id)
                            ->where('p.status', 1)
                            ->where('p.sitemap', 1)
                            ->get([
                                'p.*'
                            ]);
                        if ($_products_items->isNotEmpty()) {
                            $_products_items->map(function ($_product) use (&$_push_items) {
                                $_push_items->push([
                                    'name'          => $_product->title,
                                    'url'           => $_product->_alias->language != DEFAULT_LANGUAGE ? _u("{$_product->_alias->language}/{$_product->_alias->alias}") : _u($_product->_alias->alias),
                                    'last_modified' => $_product->_last_modified(),
                                    'items'         => NULL
                                ]);
                            });
                        }
                        $_items->push([
                            'name'          => $_category->title,
                            'url'           => $_category->_alias->language != DEFAULT_LANGUAGE ? _u("{$_category->_alias->language}/{$_category->_alias->alias}") : _u($_category->_alias->alias),
                            'last_modified' => $_category->_last_modified(),
                            'items'         => $_push_items
                        ]);
                    });
                }

                $_shop_filter_category_page = ShopFilterParamsPage::language($_language)
                    ->where('status', 1)
                    ->where('show', 1)
                    ->orderBy('id')
                    ->get();
                if ($_shop_filter_category_page->isNotEmpty()) {
                    $_shop_filter_category_page->map(function ($_page) use (&$_items) {
                        $_items->push([
                            'name'          => $_page->title,
                            'url'           => $_page->_alias->langauge != DEFAULT_LANGUAGE ? _u("{$_page->_alias->language}/{$_page->_alias->alias}") : _u($_page->_alias->alias),
                            'last_modified' => $_page->_last_modified(),
                            'items'         => NULL
                        ]);
                    });
                }

                return $_items;
            });

            return $_items;
        }

        public static function _list($full = TRUE)
        {
            $_items = collect([]);
            if ($_page_front = Page::where('type', 'front')
                ->active()
                ->get()) {
                $_page_front->map(function ($_page) use (&$_items) {
                    $_items->push([
                        'name'          => $_page->title,
                        'url'           => $_page->language != DEFAULT_LANGUAGE ? "/{$_page->language}/" : '/',
                        'last_modified' => $_page->_last_modified(),
                        'items'         => NULL
                    ]);
                });
            }
            if ($full) {
                $_url_items = UrlAlias::where('model_type', '<>', 'App\\Models\\ShopFilterParamsPage')
                    ->get();
                if ($_url_items->isNotEmpty()) {
                    $_url_items->map(function ($_url) use (&$_items) {
                        $_model = $_url->model_type;
                        $_class = class_basename($_model);
                        if ($_model = $_model::find($_url->model_id)) {
                            if ($_model->sitemap && $_model->status && ($_class != 'ShopFilterParamsPage' || ($_class == 'ShopFilterParamsPage' && $_model->show))) {
                                $_items->push([
                                    'name'          => $_model->title,
                                    'url'           => $_url->language != DEFAULT_LANGUAGE ? _u("{$_url->language}/{$_url->alias}") : _u($_url->alias),
                                    'last_modified' => $_model->_last_modified(),
                                    'items'         => NULL
                                ]);
                            }
                        }
                    });
                }
            }

            return $_items;
        }

        public static function _renderXML()
        {
            $items = self::_list();
            $xmlDom = new \DOMDocument("1.0", "utf-8");
            $urlSet = $xmlDom->createElement('urlset');
            $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlSet->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $urlSet->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
            $urlSet = $xmlDom->appendChild($urlSet);
            if ($items) {
                $_base_url = trim(config('app.url'), '/');
                $items->map(function ($item) use (&$urlSet, $xmlDom, $_base_url) {
                    $url = $xmlDom->createElement('url');
                    $url = $urlSet->appendChild($url);
                    $loc = $xmlDom->createElement('loc');
                    $loc = $url->appendChild($loc);
                    $loc->appendChild($xmlDom->createTextNode($_base_url . $item['url']));
                    $lastmod = $xmlDom->createElement('lastmod');
                    $lastmod = $url->appendChild($lastmod);
                    $lastmod->appendChild($xmlDom->createTextNode($item['last_modified']));
                });
            }
            $xmlDom->formatOutput = TRUE;
            $siteMapXML = $xmlDom->saveXML();

            header('Content-Type: text/xml; charset=UTF-8');
            echo $siteMapXML;
            exit;
        }
    }