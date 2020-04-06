<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Slider;
    use App\Models\SliderItems;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Validator;

    class SlidersController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public $title;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_sliders'
            ]);
            $this->title = [
                'index'  => trans('pages.sliders_page'),
                'create' => trans('pages.sliders_page_create'),
                'edit'   => trans('pages.sliders_page_edit')
            ];
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->theme = 'oleus.base.forms.form';
            $form->route_tag = 'sliders';
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_sliders',
                'create' => 'create_sliders',
                'update' => 'update_sliders',
                'delete' => 'delete_sliders',
            ]);
            $form->relation = array_merge($form->relation, [
                'view_link' => FALSE
            ]);
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
            $form->tabs[] = [
                'title'   => trans('others.tab_basic'),
                'content' => [
                    field_render('title', [
                        'label'      => trans('forms.label_title'),
                        'value'      => $entity->exists ? $entity->title : NULL,
                        'attributes' => [
                            'autofocus' => TRUE
                        ],
                        'required'   => TRUE
                    ]),
                    $_field_preset,
                    '<hr class="uk-divider-icon">',
                    field_render('status', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_visible_slider'),
                        'name'     => 'status',
                        'selected' => $entity->exists ? $entity->status : 1
                    ])
                ]
            ];
            if(is_null($entity->relation)) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_style'),
                    'content' => [
                        field_render('style_id', [
                            'label' => trans('forms.label_style_id'),
                            'value' => $entity->exists ? $entity->style_id : NULL
                        ]),
                        field_render('style_class', [
                            'label' => trans('forms.label_style_class'),
                            'value' => $entity->exists ? $entity->style_class : NULL,
                        ])
                    ],
                ];
            }
            if($entity->exists) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_slider_items'),
                    'content' => [
                        'section' => view('oleus.sliders.items', [
                            'items'  => $entity->_items,
                            'entity' => $entity
                        ])->render()
                    ]
                ];
            }

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required',
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => $this->title['index'],
                'seo._title'  => $this->title['index']
            ]);
            $items = Slider::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->paginate();

            return view('oleus.sliders.index', compact('items'));
        }

        public function create(Slider $item)
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
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'status',
                'style_id',
                'style_class',
                'preset',
            ]);
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $item = Slider::updateOrCreate([
                'id' => NULL
            ], $_save);

            return redirect()
                ->route('oleus.sliders.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.slider_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Slider $item)
        {
            $this->set_wrap([
                'page._title' => $this->title['edit'],
                'seo._title'  => $this->title['edit']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Slider $item)
        {
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'status',
                'style_id',
                'style_class',
                'preset',
            ]);
            $item->update($_save);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.sliders.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.slider_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.sliders')
                        ->with('notice', [
                            'message' => trans('notice.slider_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.sliders.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.slider_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Slider $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.sliders.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.slider_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.sliders')
                    ->with('notice', [
                        'message' => trans('notice.slider_deleted'),
                        'status'  => 'success'
                    ]);
            }
        }

        public function item(Request $request, Slider $slider, $action, $id = NULL)
        {
            $commands = [];
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
                        'data'    => view('oleus.sliders.item_modal', compact('item', 'slider'))
                            ->render()
                    ];
                    break;
                case 'edit':
                    $item = SliderItems::find($id);
                    $commands[] = [
                        'command' => 'modal',
                        'options' => [
                            'bgClose' => FALSE
                        ],
                        'data'    => view('oleus.sliders.item_modal', compact('item', 'slider'))
                            ->render()
                    ];
                    break;
                case 'save':
                    $_save = $request->input('slider_item');
                    if($background = $_save['background_fid']) {
                        $_background = array_shift($background);
                        Session::flash('slider_item.background_fid', json_encode([f_get($_background['id'])]));
                    }
                    $validate_rules = [
                        'slider_item.title'          => 'required',
                        'slider_item.background_fid' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        $_save['slider_id'] = $slider->id;
                        if(isset($_background)) $_save['background_fid'] = (int)$_background['id'];
                        SliderItems::updateOrCreate([
                            'id' => NULL
                        ], $_save);
                        Session::forget([
                            'slider_item.background_fid'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-sliders-items',
                            'data'    => view('oleus.sliders.item', ['items' => $slider->_items])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.slider_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'update':
                    $_save = $request->input('slider_item');
                    if($background = $_save['background_fid']) {
                        $_background = array_shift($background);
                        Session::flash('slider_item.background_fid', json_encode([f_get($_background['id'])]));
                    }
                    $validate_rules = [
                        'slider_item.title'          => 'required',
                        'slider_item.background_fid' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        if(isset($_background)) $_save['background_fid'] = (int)$_background['id'];
                        SliderItems::updateOrCreate([
                            'id' => $_save['id']
                        ], $_save);
                        Session::forget([
                            'slider_item.background_fid'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-sliders-items',
                            'data'    => view('oleus.sliders.item', ['items' => $slider->_items])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.slider_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'destroy':
                    SliderItems::find($id)
                        ->delete();
                    $slider_items = $slider->_items;
                    if($slider_items->isNotEmpty()) {
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-sliders-items',
                            'data'    => view('oleus.sliders.item', ['items' => $slider_items])
                                ->render()
                        ];
                    } else {
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-sliders-items',
                            'data'    => '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>' . trans('others.no_items') . '</div>'
                        ];
                    }
                    $commands[] = [
                        'command' => 'notifi',
                        'status'  => 'success',
                        'text'    => trans('notice.slider_item_deleted')
                    ];
                    $commands[] = [
                        'command' => 'modal_close'
                    ];
                    break;
            }
            $slider->forgetCache();

            return response($commands, 200);
        }

        public function relation(Request $request)
        {
            if($request->has('forms')) {
                $_alert = NULL;
                $_forms = $request->input('forms');
                $_entity_id = $request->input('item_id');
                $_location = $request->input('location');
                $_language = $request->input('language');
                $_validate_rules = [
                    'location' => NULL,
                    'language' => NULL
                ];
                foreach($_validate_rules as $_field => $_rule) {
                    $_field_id = str_slug($_field);
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_forms}-{$_field_id}",
                        'data'    => 'uk-form-danger'
                    ];
                }
                if(!$_location && !$_language) {
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'danger',
                        'text'    => trans('notice.select_one_of_the_fields')
                    ];
                    foreach($_validate_rules as $_field => $_rule) {
                        $_field_id = str_slug($_field);
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-{$_field_id}",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                } else {
                    $_primary = Slider::find($_entity_id);
                    if($_primary->setDuplicate($_language, $_location)) {
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => trans('notice.relate_item_generated')
                        ];
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-relation-items',
                            'data'    => view('oleus.base.forms.fields_group_relations_items', [
                                'related_items' => $_primary->related,
                                'route'         => 'sliders',
                                'form'          => $this->_form($_primary)
                            ])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'modal_close',
                            'target'  => '#modals-form-relate-items',
                        ];
                    } else {
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'danger',
                            'text'    => trans('notice.error_duplicate_record')
                        ];
                    }
                }
            } else {
                $_locations = fields_relate_locations_values();
                $_languages = fields_relate_languages_values();
                $form = parent::__form();
                $form->title = trans('forms.label_related_items');
                $form->button_name = trans('forms.button_add');
                $form->route = _r('oleus.sliders.relation');
                $form->tabs[] = field_render('forms', [
                    'type'  => 'hidden',
                    'value' => 'relation-items',
                ]);
                $form->tabs[] = field_render('item_id', [
                    'type'  => 'hidden',
                    'value' => $request->input('id'),
                ]);
                if($_locations) {
                    $form->tabs[] = field_render('location', [
                        'type'   => 'select',
                        'id'     => 'relation-items-location',
                        'label'  => trans('forms.label_related_location'),
                        'value'  => NULL,
                        'values' => $_locations,
                        'class'  => 'uk-select2'
                    ]);
                }
                if($_languages) {
                    $form->tabs[] = field_render('language', [
                        'type'   => 'select',
                        'id'     => 'relation-items-language',
                        'label'  => trans('forms.label_related_language'),
                        'value'  => NULL,
                        'values' => $_languages,
                        'class'  => 'uk-select2'
                    ]);
                }
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-relate-items',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }
    }
