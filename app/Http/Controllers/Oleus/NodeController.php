<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\Node;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;

    class NodeController extends Controller
    {
        use Dashboard;
        use Authorizable;
        protected $types;
        protected $authors;
        public $title;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_nodes'
            ]);
            $this->types = Node::_types();
            $this->authors = User::_users();
            $this->title = [
                'index'  => trans('pages.nodes_page'),
                'create' => trans('pages.nodes_page_create'),
                'edit'   => trans('pages.nodes_page_edit')
            ];
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'nodes';
            $form->seo = TRUE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_nodes',
                'create' => 'create_nodes',
                'update' => 'update_nodes',
                'delete' => 'delete_nodes',
            ]);
            $_field_type = NULL;
            if(!$entity->exists && $this->types) {
                $_field_type = field_render('entity_id', [
                    'type'     => 'select',
                    'label'    => trans('forms.label_related_page'),
                    'value'    => NULL,
                    'values'   => $this->types,
                    'class'    => 'uk-select2',
                    'required' => TRUE,
                    'help'     => trans('forms.help_related_page')
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
                    field_render('preview_fid', [
                        'type'   => 'file',
                        'label'  => trans('forms.label_preview'),
                        'allow'  => 'jpg|jpeg|gif|png|svg',
                        'values' => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                    ]),
                    field_render('teaser', [
                        'label'      => trans('forms.label_teaser'),
                        'type'       => 'textarea',
                        'value'      => $entity->exists ? $entity->teaser : NULL,
                        'attributes' => [
                            'rows' => 4,
                        ]
                    ]),
                    field_render('body', [
                        'label'      => trans('forms.label_body'),
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->exists ? $entity->body : NULL,
                        'attributes' => [
                            'rows' => 8,
                        ],
                        'required'   => TRUE,
                    ]),
                    '<hr class="uk-divider-icon">',
                    field_render('published_at', [
                        'label' => trans('forms.label_published_at'),
                        'value' => $entity->exists ? $entity->published_at->format('d.m.Y') : Carbon::now()->format('d.m.Y'),
                        'class' => 'uk-datepicker'
                    ]),
                    field_render('user_id', [
                        'type'   => 'select',
                        'label'  => trans('forms.label_author_node'),
                        'value'  => $entity->exists ? $entity->user_id : NULL,
                        'values' => $this->authors,
                        'class'  => 'uk-select2',
                    ]),
                    field_render('sort', [
                        'type'   => 'select',
                        'label'  => trans('forms.label_sort'),
                        'value'  => $entity->exists ? $entity->sort : 0,
                        'values' => sort_field(),
                        'class'  => 'uk-select2',
                    ]),
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
                        //                    field_render('background_fid', [
                        //                        'type'   => 'file',
                        //                        'label'  => trans('forms.label_background_page'),
                        //                        'allow'  => 'jpg|jpeg|gif|png|svg',
                        //                        'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                        //                    ]),
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
                    ])
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title'     => 'required',
                'body'      => 'required',
                'entity_id' => 'sometimes|required',
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => $this->title['index'],
                'seo._title'  => $this->title['index']
            ]);
            $items = Node::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderByDesc('status')
                ->orderByDesc('published_at')
                ->orderByDesc('updated_at')
                ->orderBy('title')
                ->paginate();

            return view('oleus.nodes.index', compact('items'));
        }

        public function create(Node $item)
        {
            if(is_null($this->types)) {
                return redirect()
                    ->route('oleus.nodes')
                    ->with('notice', [
                        'message' => trans('notice.node_list_pages_do_not_exist'),
                        'status'  => 'warning'
                    ]);
            }
            $this->set_wrap([
                'page._title' => $this->title['create'],
                'seo._title'  => $this->title['create']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
            }
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
                'teaser',
                'body',
                'status',
                'style_id',
                'style_class',
                'preview_fid',
                'background_fid',
                'entity_id',
                'user_id',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
                'sort',
            ]);
            if(isset($_preview_fid)) $_save['preview_fid'] = (int)$_preview_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $_save['published_at'] = ($_published_at_node = $request->input('published_at')) ? Carbon::parse($_published_at_node) : Carbon::now();
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $item = Node::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);

            return redirect()
                ->route('oleus.nodes.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.node_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Node $item)
        {
            $this->set_wrap([
                'page._title' => $this->title['edit'],
                'seo._title'  => $this->title['edit']
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Node $item)
        {
            if($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
            }
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
                'teaser',
                'body',
                'status',
                'style_id',
                'style_class',
                'preview_fid',
                'background_fid',
                'entity_id',
                'user_id',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
                'sort',
            ]);
            if(isset($_preview_fid)) $_save['preview_fid'] = (int)$_preview_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $_save['published_at'] = ($_published_at_node = $request->input('published_at')) ? Carbon::parse($_published_at_node) : Carbon::now();
            $item->update($_save);
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.nodes.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.node_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.nodes')
                        ->with('notice', [
                            'message' => trans('notice.node_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.nodes.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.node_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Node $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.nodes.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.node_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.nodes')
                    ->with('notice', [
                        'message' => trans('notice.node_deleted'),
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
                    $_primary = Node::find($_entity_id);
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
                                'route'         => 'nodes',
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
                $form->route = _r('oleus.nodes.relation');
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
