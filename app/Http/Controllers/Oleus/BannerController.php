<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Banner;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;

    class BannerController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_banners'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->theme = 'oleus.base.forms.form';
            $form->route_tag = 'banners';
            $form->relation = FALSE;
            $form->permission = [
                'read'   => 'read_banners',
                'create' => 'create_banners',
                'update' => 'update_banners',
                'delete' => 'delete_banners',
            ];
            $_field_preset = NULL;
            if($_presets = config('os_images')) {
                $_preset_values[] = trans('forms.value_choice');
                foreach($_presets as $_preset_key => $_preset_value) {
                    if(isset($_preset_value['w']) && isset($_preset_value['h'])) {
                        $_label = "{$_preset_value['w']}px * {$_preset_value['h']}px";
                    } elseif(isset($_preset_value['w'])) {
                        $_label = "{$_preset_value['w']}px * auto";
                    } elseif(isset($_preset_value['h'])) {
                        $_label = "auto * {$_preset_value['h']}px";
                    }
                    $_preset_values[$_preset_key] = $_label;
                }
                $_field_preset = field_render('preset', [
                    'label'    => trans('forms.label_preset'),
                    'type'     => 'select',
                    'selected' => $entity->exists ? $entity->preset : NULL,
                    'class'    => 'uk-select2',
                    'values'   => $_preset_values
                ]);
            }
            $_get_alias = $entity->_get_alias();
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('banner_fid', [
                            'type'   => 'file',
                            'label'  => trans('forms.label_banner'),
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $entity->exists && $entity->_banner ? [$entity->_banner] : NULL,
                        ]),
                        $_field_preset,
                        field_render('link', [
                            'type'       => 'autocomplete',
                            'label'      => trans('forms.label_composed_link'),
                            'value'      => $entity->exists && is_numeric($entity->alias_id) && $_get_alias ? $entity->alias_id : NULL,
                            'selected'   => $entity->exists && $_get_alias ? $_get_alias->name : NULL,
                            'class'      => 'uk-autocomplete',
                            'attributes' => [
                                'data-url'   => _r('oleus.menus.link'),
                                'data-value' => 'name'
                            ],
                            'help'       => trans('forms.help_link_composed_alias')
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_visible_banner'),
                            'name'     => 'status',
                            'selected' => $entity->exists ? $entity->status : 1
                        ])
                    ]
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'banner_fid' => 'required',
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.banners_page'),
                'seo._title'  => trans('pages.banners_page')
            ]);
            $items = Banner::paginate();

            return view('oleus.banners.index', compact('items'));
        }

        public function create(Banner $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.banners_page_create'),
                'seo._title'  => trans('pages.banners_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($banner_fid = $request->input('banner_fid')) {
                $_banner_fid = array_shift($banner_fid);
                Session::flash('banner_fid', json_encode([f_get($_banner_fid['id'])]));
            }
            $this->_validate($request);
            $_save = $request->only([
                'preset',
                'status',
                'banner_fid',
            ]);
            if(isset($_banner_fid)) $_save['banner_fid'] = (int)$_banner_fid['id'];
            $_save['user_id'] = $request->user()->id;
            $item = Banner::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'banner_fid'
            ]);

            return redirect()
                ->route('oleus.banners.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.banner_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Banner $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.banners_page_edit'),
                'seo._title'  => trans('pages.banners_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Banner $item)
        {
            if($banner_fid = $request->input('banner_fid')) {
                $_banner_fid = array_shift($banner_fid);
                Session::flash('banner_fid', json_encode([f_get($_banner_fid['id'])]));
            }
            $this->_validate($request);
            $_save = $request->only([
                'preset',
                'status',
                'banner_fid',
            ]);
            if(isset($_banner_fid)) $_save['banner_fid'] = (int)$_banner_fid['id'];
            $item->update($_save);
            Session::forget([
                'banner_fid'
            ]);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.banners')
                    ->with('notice', [
                        'message' => trans('notice.banner_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.banners.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.banner_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Banner $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.banners')
                ->with('notice', [
                    'message' => trans('notice.banner_deleted'),
                    'status'  => 'success'
                ]);
        }
    }
