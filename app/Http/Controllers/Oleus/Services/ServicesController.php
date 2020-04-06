<?php

    namespace App\Http\Controllers\Oleus\Services;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Profile;
    use App\Models\Service;
    use App\Models\ServicePrice;
    use App\Models\UrlAlias;
    use App\User;
    use Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use View;

    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;

    class ServicesController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            $this->middleware([
                'permission:read_services'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'services';
            $form->seo = TRUE;
            $form->location = TRUE;
            $form->permission = [
                'read'   => 'read_services',
                'create' => 'create_services',
                'update' => 'update_services',
                'delete' => 'delete_services',
            ];
            $form->tabs = [
                [
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
                        field_render('in_order_form', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_in_order_form'),
                            'selected' => $entity->exists ? $entity->in_order_form : 0
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_publish'),
                            'selected' => $entity->exists ? $entity->status : 1
                        ])
                    ]
                ],
                [
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
                        field_render('background_fid', [
                            'type'   => 'file',
                            'label'  => trans('forms.label_background_page'),
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                        ]),
                    ]
                ],
            ];
            if($entity->exists) {
                $_prices = $entity->_prices;
                $form->tabs[] = [
                    'title'   => trans('others.tab_prices'),
                    'content' => [
                        view('oleus.services.prices', [
                            'items'  => $_prices,
                            'entity' => $entity
                        ])->render()
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
                'page._title' => trans('pages.services_page'),
                'seo._title'  => trans('pages.services_page')
            ]);
            $items = Service::whereNull('location')
                ->orderByDesc('status')
                ->orderBy('title')
                ->paginate();

            return view('oleus.services.index', compact('items'));
        }

        public function create(Service $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.services_page_create'),
                'seo._title'  => trans('pages.services_page_create')
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
                'in_order_form',
            ]);
            if(isset($_background_fid)) {
                $_save['background_fid'] = (int)$_background_fid['id'];
            }
            $_save['language'] = config('app.locale');
            $item = Service::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);

            return redirect()
                ->route('oleus.services.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.service_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Service $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.services_page_edit'),
                'seo._title'  => trans('pages.services_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Service $item)
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
                'in_order_form',
            ]);
            if(isset($_background_fid)) {
                $_save['background_fid'] = (int)$_background_fid['id'];
            }
            $_save['language'] = config('app.locale');
            $item->update($_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.services')
                    ->with('notice', [
                        'message' => trans('notice.service_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.services.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.service_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Service $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.services')
                ->with('notice', [
                    'message' => trans('notice.service_deleted'),
                    'status'  => 'success'
                ]);
        }

        public function prices(Request $request, Service $service, $id = NULL)
        {
            if($id) {
                $commands[] = [
                    'command' => 'remove',
                    'target'  => "#service-price-item-{$id}"
                ];
            } else {
                $item = new ServicePrice();
                $commands[] = [
                    'command' => 'append',
                    'target'  => '#list-service-prices',
                    'data'    => view('oleus.services.item', compact('service', 'item'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function relation(Request $request, $entity_id, $location)
        {
            $_primary = Service::find($entity_id);
            $item = Service::updateOrCreate([
                'id' => NULL
            ], [
                'parent_id'            => $_primary->parent_id,
                'title'                => $_primary->title,
                'sub_title'            => $_primary->sub_title,
                'body'                 => $_primary->body,
                'background_fid'       => $_primary->background_fid,
                'meta_title'           => $_primary->meta_title,
                'meta_description'     => $_primary->meta_description,
                'meta_keywords'        => $_primary->meta_keywords,
                'meta_robots'          => $_primary->meta_robots,
                'sitemap'              => $_primary->sitemap,
                'status'               => $_primary->status,
                'style_id'             => $_primary->style_id,
                'style_class'          => $_primary->style_class,
                'language'             => $_primary->language,
                'location'             => $location,
                'in_order_form'        => 0,
                'relation_location_id' => $_primary->id,
                'access'               => 0,
            ]);
            if($_primary->_alias) {
                $_suffix_alias = config("os_contacts.cities.{$location}.{$_primary->language}.suffix_alias");
                $_generate_alias = $_primary->_alias->alias . ($_suffix_alias ? "-{$_suffix_alias}" : '');
                if(UrlAlias::where('alias', $_generate_alias)
                        ->count() > 0
                ) {
                    $index = 0;
                    while($index <= 100) {
                        $_generate_url = "{$_generate_alias}-{$index}";
                        if(UrlAlias::where('alias', $_generate_url)
                                ->count() == 0
                        ) {
                            $_generate_alias = $_generate_url;
                            break;
                        }
                        $index++;
                    }
                }
                $_alias = UrlAlias::updateOrCreate([
                    'id' => NULL,
                ], [
                    'model_id'   => $item->id,
                    'model_type' => $item->getMorphClass(),
                    'alias'      => $_generate_alias,
                    'language'   => $item->language,
                    'location'   => $item->location,
                ]);
                $item->alias_id = $_alias->id;
                $item->save();
            }

            return redirect()
                ->route('oleus.services.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.duplicate_created'),
                    'status'  => 'success'
                ]);
        }
    }
