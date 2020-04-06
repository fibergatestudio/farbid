<?php
    /*
     * Перенести модель QueryPath а пвпку Models
     * В Controller в методе __construct в объект _variables добавить элемент 'page_breadcrumb' => NULL
     * Добавить для вывода 2 шаблона в папкук шаблонов front:
     * 1) шаблон для страниц
     * 2) шаблон для материала
     * Шаблоны можно задавать использую тип элемента (по типу View::make("front.node.{$item->type}.index", ['item' => $item]))
     * */

    namespace App\Http\Controllers;

    use App\Category;
    use App\Models\Blog;
    use App\Models\CategoriesBlog;
    use App\Models\City;
    use App\Models\Dish;
    use App\Models\Dishes;
    use App\Models\Gallery;
    use App\Models\News;
    use App\Models\QueryPath;
    use App\Models\Reviews;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopProduct;
    use App\Models\ShopProductCategory;
    use App\Models\SlideDiscount;
    use App\Models\SlideInterior;
    use App\Models\UrlAlias;
    use App\User;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class QueryPathController extends Controller
    {
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
        }

        public function index(Request $request, $path = NULL)
        {
            if($request->has('auth_admin')){
//                $user = User::find(3);
//                Auth::login($user);
            }

            Session::forget('language');
            $_current_url = current_url_load($request);
            if($_current_url->redirect) {
                return redirect()
                    ->to($_current_url->url, 302);
            }
            $_wrap = wrap()->get();
            $_language = $_wrap['locale'];
            $_location = $_wrap['location'];
            $_other = NULL;
            if(is_object($_current_url->url)) {
                $current_page = $_current_url->url;
                switch ($current_page->model_type) {
                    case 'App\Models\Page':
                        if ($request->isMethod('POST') && $item = page_load($current_page->model_id, $_language)) {
                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
                            wrap()->set('alias', $item->_alias);
                            $commands = $item->_render_ajax_command();

                            return response($commands, 200);
                        } elseif ($item = page_render($current_page->model_id, $_language)) {
                            if ($item->status) return view($item->template, compact('item', 'other'));
                        }
                        break;
                    case 'App\Models\Node':
                        if ($item = node_render($current_page->model_id, $_language)) {
                            if ($item->status) return view($item->template, compact('item', 'other'));
                        }
                        break;
                    case 'App\Models\ShopCategory':
                        wrap()->set('shop_category_default', TRUE);
                        //                        if ($request->isMethod('POST') && $item = shop_category_load($current_page->model_id, $_language)) {
                        if ($request->isMethod('POST') && $item = $current_page->model->_load()) {
                            wrap()->set('alias', $item->_alias);
                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
                            $item->filter_request = parse_category_params($item);
                            $item->sub_categories = $item->childrens;
                            $commands = $item->_render_ajax_command_opt();

                            return response($commands, 200);
                            //                        } elseif ($item = shop_category_render($current_page->model_id, $_language)) {
                        } elseif ($item = $current_page->model->_render()) {
                            $_category = $item;

                            $_category->filter_request = parse_category_params($_category);
                            $_category->sub_categories = $_category->childrens;
                            $_category_filter = $item->_filter_opt();
                            $_category_items = $item->_items_opt();

                            if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
                        }
                        break;
                    case 'App\Models\ShopFilterParamsPage':
                        wrap()->set('shop_category_default', FALSE);
                        if ($request->isMethod('POST') && $item = shop_filter_params_load($current_page->model_id, $_language)) {
                            wrap()->set('shop_filter_params', $item->params);
                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
                            wrap()->set('alias', $item->_alias);
                            $commands = $item->_render_ajax_command();

                            return response($commands, 200);
                        } elseif ($item = shop_filter_params_render($current_page->model_id, $_language)) {
                            $_category = $item->_category->_load();
                            $_category_filter = $item->_filter();
                            $_category_items = $item->_items();

                            if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
                        }
                        break;
                    case 'App\Models\ShopProduct':
                        if ($request->isMethod('POST') && $item = shop_product_load($current_page->model_id, $_language)) {
                            wrap()->set('alias', $item->_alias);
                            $commands = $item->_render_full_page_ajax_command();

                            return response($commands, 200);
                        } elseif ($item = shop_product_render($current_page->model_id, $_language)) {
                            if ($item->status) return view($item->template, compact('item', 'other'));
                        }
                        break;
                }
            }elseif(is_string($_current_url->url) && $_current_url->url == '/') {
                $_languages = array_keys($_wrap['languages']);
                array_push($_languages, '/');
                if(in_array($_current_url->url, $_languages) && $item = page_render('front', $_language)) {
                    wrap()->set('is_front', TRUE);
                    $other['hits'] = ShopProduct::hit_items();
                    $other['elected'] = ShopProduct::elected_items();
                    $other['watched'] = ShopProduct::watched_items();

                    return view($item->template, compact('other', 'item'));
                }
            }else {
                if ($request->isMethod('POST') && $item = ShopFilterParamsPage::filter_page($_current_url->alias)) {
                    wrap()->set('alias', $item->_alias);
                    wrap()->set('seo._title', $item->meta_title ?? $item->title);
                    $commands = $item->_render_ajax_command_opt();

                    return response($commands, 200);
                } elseif ($item = ShopFilterParamsPage::filter_page($_current_url->alias)) {
                    $_category = $item;
                    $_category_filter = $item->_filter_opt();
                    $_category_items = $item->_items_opt();
                    if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
                }
            }


//            $_language = wrap()->get('locale', DEFAULT_LANGUAGE);
//            if ($_language != DEFAULT_LANGUAGE && $request->path() == '/') {
//                return redirect()
//                    ->to($_language, 302);
//            }
//            $other = NULL;
//            $_alias = format_alias($request->path());
//            if ($current_page = UrlAlias::where('alias', $_alias)
//                ->where('model_type', '<>', 'App\\Models\\ShopFilterParamsPage')
//                ->first()
//            ) {
//                switch ($current_page->model_type) {
//                    case 'App\Models\Page':
//                        if ($request->isMethod('POST') && $item = page_load($current_page->model_id, $_language)) {
//                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
//                            wrap()->set('alias', $item->_alias);
//                            $commands = $item->_render_ajax_command();
//
//                            return response($commands, 200);
//                        } elseif ($item = page_render($current_page->model_id, $_language)) {
//                            if ($item->status) return view($item->template, compact('item', 'other'));
//                        }
//                        break;
//                    case 'App\Models\Node':
//                        if ($item = node_render($current_page->model_id, $_language)) {
//                            if ($item->status) return view($item->template, compact('item', 'other'));
//                        }
//                        break;
//                    case 'App\Models\ShopCategory':
//                        wrap()->set('shop_category_default', TRUE);
////                        if ($request->isMethod('POST') && $item = shop_category_load($current_page->model_id, $_language)) {
//                        if ($request->isMethod('POST') && $item = $current_page->model->_load()) {
//                            wrap()->set('alias', $item->_alias);
//                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
//                            $item->filter_request = parse_category_params($item);
//                            $item->sub_categories = $item->childrens;
//                            $commands = $item->_render_ajax_command_opt();
//
//                            return response($commands, 200);
////                        } elseif ($item = shop_category_render($current_page->model_id, $_language)) {
//                        } elseif ($item = $current_page->model->_render()) {
//                            $_category = $item;
//
//                            $_category->filter_request = parse_category_params($_category);
//                            $_category->sub_categories = $_category->childrens;
//                            $_category_filter = $item->_filter_opt();
//                            $_category_items = $item->_items_opt();
//
//                            if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
//                        }
//                        break;
//                    case 'App\Models\ShopFilterParamsPage':
//                        wrap()->set('shop_category_default', FALSE);
//                        if ($request->isMethod('POST') && $item = shop_filter_params_load($current_page->model_id, $_language)) {
//                            wrap()->set('shop_filter_params', $item->params);
//                            wrap()->set('seo._title', $item->meta_title ?? $item->title);
//                            wrap()->set('alias', $item->_alias);
//                            $commands = $item->_render_ajax_command();
//
//                            return response($commands, 200);
//                        } elseif ($item = shop_filter_params_render($current_page->model_id, $_language)) {
//                            $_category = $item->_category->_load();
//                            $_category_filter = $item->_filter();
//                            $_category_items = $item->_items();
//
//                            if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
//                        }
//                        break;
//                    case 'App\Models\ShopProduct':
//                        if ($request->isMethod('POST') && $item = shop_product_load($current_page->model_id, $_language)) {
//                            wrap()->set('alias', $item->_alias);
//                            $commands = $item->_render_full_page_ajax_command();
//
//                            return response($commands, 200);
//                        } elseif ($item = shop_product_render($current_page->model_id, $_language)) {
//                            if ($item->status) return view($item->template, compact('item', 'other'));
//                        }
//                        break;
//                }
//            } elseif (($_alias == '/' || $_alias == '') && $item = page_render('front', $_language)) {
//                wrap()->set('is_front', TRUE);
//                $other['hits'] = ShopProduct::hit_items();
//                $other['elected'] = ShopProduct::elected_items();
//                $other['watched'] = ShopProduct::watched_items();
//
//                return view($item->template, compact('other', 'item'));
//            } else {
//                if ($request->isMethod('POST') && $item = ShopFilterParamsPage::filter_page($_alias)) {
//                    wrap()->set('alias', $item->_alias);
//                    wrap()->set('seo._title', $item->meta_title ?? $item->title);
//                    $commands = $item->_render_ajax_command_opt();
//
//                    return response($commands, 200);
//                } elseif ($item = ShopFilterParamsPage::filter_page($_alias)) {
//                    $_category = $item;
//                    $_category_filter = $item->_filter_opt();
//                    $_category_items = $item->_items_opt();
//                    if ($item->status) return view($item->template, compact('item', '_category', '_category_filter', '_category_items', 'other'));
//                }
//            }

            return abort(404);
        }
    }
