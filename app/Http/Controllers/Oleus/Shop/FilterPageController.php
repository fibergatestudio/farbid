<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopParam;
    use App\Models\ShopProduct;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\DB;

    class FilterPageController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_categories'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_filter_pages';
            $form->seo = TRUE;
            $form->relation = FALSE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_shop_categories',
                'update' => 'update_shop_categories',
                'delete' => 'delete_shop_categories',
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('title', [
                            'label'      => trans('forms.label_title'),
                            'value'      => $entity->title ?? NULL,
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('sub_title', [
                            'label' => trans('forms.label_sub_title'),
                            'value' => $entity->sub_title ?? NULL
                        ]),
                        field_render('body', [
                            'label'      => trans('forms.label_body'),
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->body ?? NULL,
                            'attributes' => [
                                'rows' => 8,
                            ]
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_publish'),
                            'selected' => $entity->status ?? 1
                        ])
                    ]
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required'
            ]);
        }

        public function index()
        {
            $_filter = \request()->only([
                'title',
                'category',
                'language',
            ]);
            $this->set_wrap([
                'page._title' => trans('pages.shop_filter_page_page'),
                'seo._title'  => trans('pages.shop_filter_page_page')
            ]);
            $items = ShopFilterParamsPage::orderBy('title')
                ->where('show', 1)
                ->when($_filter, function ($query) use ($_filter) {
                    if($_filter['language']) {
                        $query->where('language', $_filter['language']);
                    }
                    if($_filter['title']) {
                        $query->where('title', 'like', "%{$_filter['title']}%");
                    }
                    if($_filter['category'] > 0) {
                        $_category = ShopCategory::find($_filter['category']);
                        $_childrens = $_category->childrens;
                        if($_childrens) {
                            $query->whereIn('category_id', $_childrens->keyBy('id')->keys());
                        } else {
                            $query->where('category_id', $_filter['category']);
                        }
                    }
                })
                ->orderBy('title');
            if(!$_filter) $items->where('language', DEFAULT_LANGUAGE);
            $items = $items->paginate();
            $_categories_all = ShopCategory::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->pluck('title', 'id');
            $_categories_all = $_categories_all->map(function ($_name_category, $_id_category) {
                $_category_model = ShopCategory::find($_id_category);
                if($_parents = $_category_model->parents) {
                    $_output = NULL;
                    foreach($_parents as $_parent) $_output[] = $_parent->title;
                    $_output[] = $_name_category;

                    return implode(' / ', $_output);
                }

                return $_name_category;
            })->sort()->prepend('Выбрать категорию', 0)->all();

            return view('oleus.shop.index_filter_pages', compact('items', '_categories_all'));
        }

        public function edit(ShopFilterParamsPage $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_filter_page_edit'),
                'seo._title'  => trans('pages.shop_filter_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopFilterParamsPage $item)
        {
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'status',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
            ]);
            $item->update($_save);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.shop_filter_pages')
                    ->with('notice', [
                        'message' => trans('notice.page_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.shop_filter_pages.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.page_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, ShopFilterParamsPage $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.shop_filter_pages')
                ->with('notice', [
                    'message' => trans('notice.page_deleted'),
                    'status'  => 'success'
                ]);

        }
    }