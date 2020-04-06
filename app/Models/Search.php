<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\DB;
    use Morphy;

    class Search extends Model
    {
        public static function conversion_query_string($query_string)
        {
            $_search_string = mb_strtoupper($query_string);
            $_search[] = $_search_string;
            $_search_type = [
                1,
                2
            ];
            if ($_search_type) {
                if (in_array(1, $_search_type) && in_array(2, $_search_type)) {
                    $_explode_string = explode(' ', $_search_string);
                    $_morphy = Morphy::getPseudoRoot($_search_string);
                    $_words = $_morphy ? : [];
                    if (count($_explode_string) > 1) {
                        foreach ($_explode_string as $string) {
                            if (mb_strlen($string) >= 3) {
                                $_morphy = Morphy::getPseudoRoot($string);
                                if ($_morphy) $_words = array_merge($_words, $_morphy);
                            }
                        }
                    }
                    if ($_words) {
                        foreach ($_words as $item) {
                            if (!in_array($item, $_search)) $_search[] = $item;
                        }
                    }
                } elseif (in_array(1, $_search_type)) {
                    $_morphy = Morphy::getPseudoRoot($_search_string);
                    if ($_morphy) foreach ($_morphy as $item) $_search[] = $item;
                } elseif (in_array(2, $_search_type)) {
                    $_explode_string = explode(' ', $_search_string);
                    if (count($_explode_string) > 1) foreach ($_explode_string as $string) if (mb_strlen($string) >= 3) $_search[] = $string;
                }
            }

            return $_search;
        }

        public static function query_search($query_string = NULL, $view = 'page')
        {
            $query_string = $query_string ?? request()->get('query_string');

            $_search_query = self::conversion_query_string($query_string);

            $_search_places = [
                'title',
                //                'body'
            ];
            $_order_query = NULL;
            foreach ($_search_query as $q) {
                $_order_query[] = "WHEN (`title` = '{$q}')";
                $_order_query[] = "WHEN (`title` = '{$q}%')";
                $_order_query[] = "WHEN (`title` = '%{$q}%')";
            }
            $i = -1;
            $_order_query_str = NULL;
            foreach ($_order_query as $_str) {
                if ($i == -1) {
                    $_order_query_str .= "CASE {$_str}";
                } else {
                    $_order_query_str .= " THEN {$i} {$_str}";
                }
                $i++;
            }
            $_order_query_str .= " THEN {$i} END DESC";
            $_language = wrap()->get('locale');
            $_this_object = page_load('search', $_language);
            $_current_page = currentPage();
            $_response = NULL;
            $items = ShopProduct::active()
                ->where('language', $_language)
                ->where(function ($query) use ($_search_query, $_search_places) {
                    $first = TRUE;
                    foreach ($_search_places as $_place) {
                        foreach ($_search_query as $string) {
                            if ($first) {
                                $query->where("{$_place}", 'like', "%{$string}%");
                                $first = FALSE;
                            } else {
                                $query->orWhere("{$_place}", 'like', "%{$string}%");
                            }
                        }
                    }
                })
                ->distinct()
                ->groupBy('id')
                ->orderByRaw($_order_query_str)
                ->orderBy('title');

            //            dd($items->toSql());

            //                ->orderBy('out_of_stock')
            //                ->orderByDesc('fasten_to_top')
            //                ->orderByDesc('ordered')
            //                ->orderByDesc('sort');
            if ($view == 'page') {
                $_wrap = wrap()->get();
                $items = $items->select(['id']);
                if ($_current_page) {
                    Paginator::currentPageResolver(function () use ($_current_page) {
                        return $_current_page;
                    });
                }
                $items = $items->paginate(7);
                if ($items->isNotEmpty() && count($items->items())) {
                    $items->getCollection()->transform(function ($_product) use ($_language) {
                        return shop_product_load($_product->id, $_language, 'short');
                    });
                }
                $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
                $_current_page = $items->currentPage();
                $_next_page = $_current_page + 1;
                $_prev_page = ($_prev = $_current_page - 1) && $_prev > 0 ? $_prev : 1;
                $_query_string = NULL;
                $_next_page_link = NULL;
                $_prev_page_link = NULL;
                if ($_request_query_array = request()->query()) {
                    unset($_request_query_array['page']);
                    if (count($_request_query_array)) {
                        foreach ($_request_query_array as $query => $value) {
                            if (is_string($value)) {
                                $_query_string[] = "{$query}={$value}";
                            } elseif (is_array($value)) {
                                foreach ($value as $_val) $_query_string[] = "{$query}[]={$_val}";
                            }
                        }
                        $_query_string = $_query_string ? '?' . implode('&', $_query_string) : '';
                    }
                }
                if ($_current_page < $items->lastPage()) {
                    $url = trim($_current_url, '/') . "/page-{$_next_page}";
                    $_next_page_link = _u($url) . $_query_string;
                }
                if ($_current_page > 2) {
                    $url = trim($_current_url, '/') . "/page-{$_prev_page}";
                    $_prev_page_link = _u($url) . $_query_string;
                } else {
                    $url = trim($_current_url, '/');
                    $_prev_page_link = _u($url) . $_query_string;
                }
                wrap()->set('seo._link_prev', $_prev_page_link);
                wrap()->set('seo._link_next', $_next_page_link);
                wrap()->set('seo._page_number', $_current_page);
                if ($_current_page > 1) {
                    wrap()->set('seo._robots', 'noindex, nofollow');
                    wrap()->set('seo._title_suffix', ' - ' . trans('others.page_full', ['page' => $_current_page]) . ' ' . wrap()->get('seo._title_suffix'));
                    wrap()->set('seo._description', ($_this_object->meta_description ?? $_wrap['seo']['_description']) . ' - ' . trans('others.page_full', ['page' => $_current_page]));
                    wrap()->set('page._title', $_this_object->title . ' - <i class="page-number">' . trans('others.page_full', ['page' => $_current_page]) . '</i>');
                    wrap()->set('breadcrumb', breadcrumb_render(['entity' => $_this_object]), TRUE);
                }

                return $items;
                //
                //
                //
                //
                //                $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
                //                $_next_page = $items->currentPage()+1;
                //                $_prev_page = ($_prev = $items->currentPage()-1) && $_prev > 0 ? $_prev : 1;
                //                $_query_string = NULL;
                //                $_next_page_link = NULL;
                //                $_prev_page_link = NULL;
                //                if($queryArray = request()->query()) {
                //                    unset($queryArray['page']);
                //                    if(count($queryArray)) {
                //                        foreach($queryArray as $query => $value) {
                //                            if(is_string($value)) {
                //                                $_query_string[] = "{$query}={$value}";
                //                            } elseif(is_array($value)) {
                //                                foreach($value as $_val) $_query_string[] = "{$query}[]={$_val}";
                //                            }
                //                        }
                //                        $_query_string = $_query_string ? '?' . implode('&', $_query_string) : '';
                //                    }
                //                }
                //                if($items->currentPage() < $items->lastPage()) {
                //                    $url = trim($_current_url, '/') . "/page-{$_next_page}";
                //                    $_next_page_link = _u($url) . $_query_string;
                //                }
                //                if($items->currentPage() > 2) {
                //                    $url = trim($_current_url, '/') . "/page-{$_prev_page}";
                //                    $_prev_page_link = _u($url) . $_query_string;
                //                } else {
                //                    $url = trim($_current_url, '/');
                //                    $_prev_page_link = _u($url) . $_query_string;
                //                }
                //                wrap()->set('seo._link_prev', $_next_page_link);
                //                wrap()->set('seo._link_next', $_prev_page_link);
                //                wrap()->set('seo._page_number', $items->currentPage());
                //                wrap()->set('seo._canonical', $_current_url);
                //                if($items->currentPage() > 1) {
                //                    $_title = wrap()->get('page._title') . '<span class="page-number uk-text-lowercase"> - ' . trans('others.page_full',
                //                            ['page' => $items->currentPage()]) . '</span>';
                //                    $_description = wrap()->get('seo._description') . ' - ' . trans('others.page_full',
                //                            ['page' => $items->currentPage()]);
                //                    $_suffix = '- ' . trans('others.page_full',
                //                            ['page' => $items->currentPage()]) . wrap()->get('seo._title_suffix');
                //                    wrap()->set('seo._title_suffix', $_suffix);
                //                    wrap()->set('seo._description', $_description);
                //                    wrap()->set('page._title', $_title);
                //                }
            } else {
                $_response = [
                    'categories' => compact([]),
                    'items'      => compact([])
                ];
                $_categories = DB::table('shop_products as sp')
                    ->rightJoin('shop_product_categories as spc', 'spc.product_id', '=', 'sp.id')
                    ->rightJoin('shop_categories as sc', 'spc.category_id', '=', 'sc.id');
                //                $_default_language = DEFAULT_LANGUAGE;
                //                if($_language != DEFAULT_LANGUAGE) {
                //                    $_categories->where('sp.language', $_default_language)
                //                        ->orWhere('sp.language', $_language)
                //                        ->orderByRaw("CASE WHEN (`sp.language` = '{$_language}') THEN 0 WHEN (`sp.language` = '{$_default_language}') THEN 1 END");
                //                } else {
                //                    $_categories->where('language', $_language);
                //                }
                $_categories->where('sp.status', 1)
                    ->where(function ($query) use ($_search_query, $_search_places) {
                        $first = TRUE;
                        foreach ($_search_places as $_place) {
                            foreach ($_search_query as $string) {
                                if ($first) {
                                    $query->where("sp.{$_place}", 'like', "%{$string}%");
                                    $first = FALSE;
                                } else {
                                    $query->orWhere("sp.{$_place}", 'like', "%{$string}%");
                                }
                            }
                        }
                    })
                    ->orderBy('sc.sort')
                    ->orderBy('sc.title');
                $_categories = $_categories->distinct()
                    ->get([
                        'sc.id',
                        'sc.sort',
                        'sc.title',
                    ]);
                if ($_categories->isNotEmpty()) {
                    $_response['categories'] = $_categories->map(function ($_category) use ($_language) {
                        return shop_category_load($_category->id, $_language);
                    });
                }
                $_products = $items->select(['id'])
                    ->paginate(5);
                if ($_products->isNotEmpty() && count($_products->items())) {
                    $_products->getCollection()->transform(function ($_product) use ($_language) {
                        return shop_product_load($_product->id, $_language);
                    });
                    $_response['items'] = $_products;
                }
            }

            return $_response;
        }

        public static function showFound($text)
        {
            $query_string = request()->input('query_string');
            $first_position = FALSE;
            $found_query =
            $found_string = NULL;

            if ($query_string) {
                $_search_query = self::conversionQueryString($query_string);
                $text = strip_tags($text);
                foreach ($_search_query as $query) {
                    if (is_bool($first_position)) {
                        $first_position = mb_strpos(mb_strtoupper($text), mb_strtoupper($query));
                        $found_query = $query;
                    }
                }
                if ($first_position) {
                    $begin_text = ($first_position - 300) < 0 ? 0 : $first_position - 300;
                    $found_text = mb_substr($text, $begin_text, 600);
                    $found_string = preg_replace("/{$found_query}/iu", "<strong>$0</strong>", $found_text);
                }
            }

            return $found_string;

        }
    }
