<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Menu;
    use App\Models\MenuItems;
    use App\Models\UrlAlias;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Validator;

    class MenuController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public $title;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_menus'
            ]);
            $this->title = [
                'index'  => trans('pages.menus_page'),
                'create' => trans('pages.menus_page_create'),
                'edit'   => trans('pages.menus_page_edit')
            ];
        }

        protected function _form($entity)
        {
            $_locations = NULL;
            $form = parent::__form();
            $form->theme = 'oleus.base.forms.form';
            $form->route_tag = 'menus';
            $form->relation = FALSE;
            $form->permission = [
                'read'   => 'read_menus',
                'create' => 'create_menus',
                'update' => 'update_menus',
                'delete' => 'delete_menus',
            ];
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('key', [
                            'label'      => trans('forms.label_machine_name'),
                            'value'      => $entity->exists ? $entity->key : NULL,
                            'required'   => TRUE,
                            'attributes' => $entity->exists ? ['disabled' => TRUE] : ['autofocus' => TRUE],
                        ]),
                        field_render('title', [
                            'label'    => trans('forms.label_name'),
                            'value'    => $entity->exists ? $entity->title : NULL,
                            'required' => TRUE
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_publish'),
                            'selected' => $entity->exists ? $entity->status : 1
                        ])
                    ]
                ]
            ];
            if($entity->exists) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_menu_items'),
                    'content' => [
                        view('oleus.menus.items', [
                            'items'  => $entity->_parents_item,
                            'entity' => $entity
                        ])->render()
                    ]
                ];
            }

            return $form;
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => $this->title['index'],
                'seo._title'  => $this->title['index']
            ]);
            $items = Menu::paginate();

            return view('oleus.menus.index', compact('items'));
        }

        public function create(Menu $item)
        {
            $this->set_wrap([
                'page._title' => $this->title['create'],
                'seo._title'  => $this->title['create']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'key'   => 'required|unique:menus,key',
                'title' => 'required'
            ]);
            $_save = $request->only([
                'key',
                'title',
                'location',
                'status',
            ]);
            $item = Menu::updateOrCreate([
                'id' => NULL
            ], $_save);

            return redirect()
                ->route('oleus.menus.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.menu_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Menu $item)
        {
            $this->set_wrap([
                'page._title' => $this->title['edit'],
                'seo._title'  => $this->title['edit']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Menu $item)
        {
            $this->validate($request, [
                'title' => 'required',
            ]);
            $_save = $request->only([
                'title',
                'status',
                'location',
            ]);
            $item->update($_save);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.menus')
                    ->with('notice', [
                        'message' => trans('notice.menu_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.menus.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.menu_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Menu $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.menus')
                ->with('notice', [
                    'message' => trans('notice.menu_deleted'),
                    'status'  => 'success'
                ]);
        }

        public function item(Request $request, Menu $menu, $action, $id = NULL)
        {
            $commands = [];
            $_parents = MenuItems::where('menu_id', $menu->id);
            if($id) {
                $_parents->where('id', '<>', $id);
            }
            $_parents = $_parents->orderBy('sort')
                ->get();
            $parents = NULL;
            if ($_parents) {
                $parents[] = ' - ';
                foreach ($_parents as $_id => $parent) {
                    $option = null;
                    if($parent->parents){
                        $option = collect($parent->parents)->map(function($_i){
                            return $_i->title;
                        })->toArray();
                    }
                    $option[] = $parent->title;
                    $parents[$parent->id] = implode(' / ', $option);
                }
            }
            switch($action) {
                case 'add':
                    $item = (object)[
                        'exists' => FALSE
                    ];
                    $commands[] = [
                        'command' => 'modal',
                        'options' => [
                            'bgClose' => FALSE
                        ],
                        'data'    => view('oleus.menus.item_modal', compact('item', 'parents', 'menu'))
                            ->render()
                    ];
                    $commands[] = [
                        'command' => 'easyAutocomplete'
                    ];
                    break;
                case 'edit':
                    $item = MenuItems::find($id);
                    $commands[] = [
                        'command' => 'modal',
                        'options' => [
                            'bgClose' => FALSE
                        ],
                        'data'    => view('oleus.menus.item_modal', compact('item', 'parents', 'menu'))
                            ->render()
                    ];
                    $commands[] = [
                        'command' => 'easyAutocomplete'
                    ];
                    break;
                case 'save':
                    $item = $request->input('menu_item');
                    if($icon = $item['data']['icon']) {
                        $_icon = array_shift($icon);
                        Session::flash('menu_item.data.icon', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'menu_item.name'      => 'required',
                        'menu_item.link.name' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        if($field == 'menu_item.link.name') $field = 'menu_item.link';
                        $field = str_replace('_', '-', $field);
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#form-field-' . str_replace('.', '-', $field),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            if($field == 'menu_item.link.name') $field = 'menu_item.link';
                            $field = str_replace('_', '-', $field);
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#form-field-' . str_replace('.', '-', $field),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        $menu_items = new MenuItems($menu);
                        $menu_items->set($item);
                        $items = $menu->_parents_item;
                        $items_output = view('oleus.menus.items_table', compact('items'))
                            ->render();
                        Session::forget([
                            'menu_item.data.icon'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-menu-items',
                            'data'    => $items_output
                        ];
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => trans('notice.menu_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'update':
                    $item = $request->input('menu_item');
                    if($icon = $item['data']['icon']) {
                        $_icon = array_shift($icon);
                        Session::flash('menu_item.data.icon', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'menu_item.name'      => 'required',
                        'menu_item.link.name' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        if($field == 'menu_item.link.name') $field = 'menu_item.link';
                        $field = str_replace('_', '-', $field);
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#form-field-' . str_replace('.', '-', $field),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            if($field == 'menu_item.link.name') $field = 'menu_item.link';
                            $field = str_replace('_', '-', $field);
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#form-field-' . str_replace('.', '-', $field),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        $menu_items = new MenuItems($menu);
                        $menu_items->set($item);
                        $items = $menu->_parents_item;
                        $items_output = view('oleus.menus.items_table', compact('items'))
                            ->render();
                        Session::forget([
                            'menu_item.data.icon'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-menu-items',
                            'data'    => $items_output
                        ];
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => trans('notice.menu_item_updated')
                        ];
                        $commands[] = [
                            'command' => 'modal_close',
                            'target'  => ''
                        ];
                    }
                    break;
                case 'destroy':
                    MenuItems::find($id)
                        ->delete();
                    MenuItems::where('parent_id', $id)
                        ->update([
                            'parent_id' => NULL
                        ]);
                    $items = $menu->_parents_item;
                    $items_output = '';
                    if($items->isNotEmpty()) {
                        $items_output = view('oleus.menus.items_table', compact('items'))
                            ->render();
                    } else {
                        $items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>' . trans('others.item_list_is_empty') . '</div>';
                    }
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#list-menu-items',
                        'data'    => $items_output
                    ];
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'success',
                        'text'    => trans('notice.menu_item_deleted')
                    ];
                    $commands[] = [
                        'command' => 'modal_close',
                        'target'  => ''
                    ];
                    break;
            }
            $menu->forgetCache();

            return response($commands, 200);
        }

        public function link(Request $request)
        {
            $items = [];

            if($search = $request->input('search')) {
                $url = new UrlAlias();
                $items = $url->_items_for_menu($search);
            }

            return response($items, 200);
        }

    }
