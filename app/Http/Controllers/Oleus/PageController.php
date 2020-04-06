<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\Page;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;

    class PageController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public $title;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_pages'
            ]);
            $this->title = [
                'index'  => trans('pages.pages_page'),
                'create' => trans('pages.pages_page_create'),
                'edit'   => trans('pages.pages_page_edit')
            ];
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'pages';
            $form->seo = TRUE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_pages',
                'create' => 'create_pages',
                'update' => 'update_pages',
                'delete' => 'delete_pages',
            ]);
            $_field_type = NULL;
            if(!$entity->exists) {
                $_field_type = field_render('type', [
                    'type'   => 'select',
                    'label'  => trans('forms.label_type'),
                    'value'  => 'normal_page',
                    'values' => [
                        'page_normal'     => trans('pages.pages_normal'),
                        'page_list_nodes' => trans('pages.pages_list_nodes')
                    ],
                    'class'  => 'uk-select2'
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
                    field_render('sub_title', [
                        'label' => trans('forms.label_sub_title'),
                        'value' => $entity->exists ? $entity->sub_title : NULL
                    ]),
                    $_field_type,
                    field_render('body', [
                        'label'      => trans('forms.label_body'),
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->exists ? $entity->body : NULL,
                        'attributes' => [
                            'rows' => 8,
                        ]
                    ]),
                    '<hr class="uk-divider-icon">',
                    field_render('status', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_publish'),
                        'selected' => $entity->exists ? $entity->status : 1
                    ])
                ]
            ];
            if(is_null($entity->relation)) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_style'),
                    'content' => [
                        field_render('style_id', [
                            'label' => trans('forms.label_style_page_id'),
                            'value' => $entity->exists ? $entity->style_id : NULL
                        ]),
                        field_render('style_class', [
                            'label' => trans('forms.label_style_page_class'),
                            'value' => $entity->exists ? $entity->style_class : NULL,
                        ]),
                        //                        field_render('background_fid', [
                        //                            'type'   => 'file',
                        //                            'label'  => trans('forms.label_background_page'),
                        //                            'allow'  => 'jpg|jpeg|gif|png|svg',
                        //                            'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                        //                        ]),
                    ]
                ];
            }
            $form->tabs[] = [
                'title'   => trans('others.tab_media'),
                'content' => [
                    field_render('medias', [
                        'type'     => 'file',
                        'label'    => trans('forms.label_medias'),
                        'multiple' => TRUE,
                        'values'   => $entity->exists && ($_medias = $entity->_medias()) ? $_medias : NULL
                    ]),
                    field_render('files', [
                        'type'     => 'file',
                        'label'    => trans('forms.label_files'),
                        'multiple' => TRUE,
                        'allow'    => 'txt|doc|docx|xls|xlsx|pdf',
                        'values'   => $entity->exists && ($_files = $entity->_medias('files')) ? $_files : NULL,
                        'help'     => trans('forms.help_upload_file_allow', ['allow' => 'txt, doc, docx, xls, xlsx ,pdf'])
                    ])
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
            $this->set_wrap([
                'page._title' => $this->title['index'],
                'seo._title'  => $this->title['index']
            ]);
            $items = Page::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderByDesc('status')
                ->orderBy('sort')
                ->orderBy('title')
                ->paginate();

            return view('oleus.pages.index', compact('items'));
        }

        public function create(Page $item)
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
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'status',
                'style_id',
                'style_class',
                'background_fid',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
            ]);
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            if($request->has('type')) $_save['type'] = $request->get('type');
            $_save['user_id'] = $request->user()->id;
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $item = Page::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);

            return redirect()
                ->route('oleus.pages.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.page_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Page $item)
        {
            $this->set_wrap([
                'page._title' => $this->title['edit'],
                'seo._title'  => $this->title['edit']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Page $item)
        {
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'status',
                'style_id',
                'style_class',
                'background_fid',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
            ]);
            if(isset($_background_fid)) $_save['background_fid'] = $_background_fid['id'];
            $item->update($_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.pages.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.page_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.pages')
                        ->with('notice', [
                            'message' => trans('notice.page_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.pages.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.page_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Page $item)
        {
            $_relation = $item->relation;
            if($item->blocked) {
                return redirect()
                    ->route('oleus.pages.edit', [
                        'id' => $item->id
                    ])
                    ->with('notice', [
                        'message' => trans('notice.action_is_blocked'),
                        'status'  => 'warning'
                    ]);
            }
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.pages.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.page_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.pages')
                    ->with('notice', [
                        'message' => trans('notice.page_deleted'),
                        'status'  => 'success'
                    ]);
            }
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
                    $_primary = Page::find($_entity_id);
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
                                'route'         => 'pages',
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
                $form->route = _r('oleus.pages.relation');
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
