<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Review;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;

    class ReviewController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_reviews'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->theme = 'oleus.base.forms.form';
            $form->route_tag = 'reviews';
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_reviews',
                'create' => 'create_reviews',
                'update' => 'update_reviews',
                'delete' => 'delete_reviews',
            ]);
            $form->relation = FALSE;
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('name', [
                            'label'      => trans('forms.label_first_name'),
                            'value'      => $entity->exists ? $entity->name : NULL,
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('subject', [
                            'label' => trans('forms.label_subject'),
                            'value' => $entity->exists ? $entity->subject : NULL,
                        ]),
                        field_render('review', [
                            'label'      => trans('forms.label_review_body'),
                            'type'       => 'textarea',
                            'value'      => $entity->exists ? $entity->review : NULL,
                            'attributes' => [
                                'rows' => 6,
                            ],
                            'required'   => TRUE
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('rating', [
                            'label'  => trans('forms.label_rating'),
                            'type'   => 'select',
                            'value'  => $entity->exists ? $entity->rating : NULL,
                            'values' => [
                                0 => trans('forms.value_choice'),
                                1 => plural_string(1, [
                                    trans('others.plural_star'),
                                    trans('others.plural_stars'),
                                    trans('others.plural_stars2')
                                ]),
                                2 => plural_string(2, [
                                    trans('others.plural_star'),
                                    trans('others.plural_stars'),
                                    trans('others.plural_stars2')
                                ]),
                                3 => plural_string(3, [
                                    trans('others.plural_star'),
                                    trans('others.plural_stars'),
                                    trans('others.plural_stars2')
                                ]),
                                4 => plural_string(4, [
                                    trans('others.plural_star'),
                                    trans('others.plural_stars'),
                                    trans('others.plural_stars2')
                                ]),
                                5 => plural_string(5, [
                                    trans('others.plural_star'),
                                    trans('others.plural_stars'),
                                    trans('others.plural_stars2')
                                ]),
                            ]
                        ]),
                        field_render('check', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_checked'),
                            'selected' => $entity->exists ? $entity->check : 0
                        ])
                    ]
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'name'   => 'required',
                'review' => 'required',
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.reviews_page'),
                'seo._title'  => trans('pages.reviews_page')
            ]);
            $items = Review::orderBy('status')
                ->orderBy('check')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.reviews.index', compact('items'));
        }

        public function create(Review $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.reviews_page_create'),
                'seo._title'  => trans('pages.reviews_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            $this->_validate($request);
            $_save = $request->only([
                'name',
                'review',
                'subject',
                'rating',
                'check'
            ]);
            $_save['status'] = 1;
            $item = Review::updateOrCreate([
                'id' => NULL
            ], $_save);

            return redirect()
                ->route('oleus.reviews.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.review_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Review $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.reviews_page_edit'),
                'seo._title'  => trans('pages.reviews_page_edit')
            ]);
            $form = $this->_form($item);
            if($item->status == 0) {
                $item->update([
                    'status' => 1
                ]);
            }

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Review $item)
        {
            $this->_validate($request);
            $_save = $request->only([
                'name',
                'review',
                'subject',
                'rating',
                'check'
            ]);
            $item->update($_save);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.reviews')
                    ->with('notice', [
                        'message' => trans('notice.review_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.reviews.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.review_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Review $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.reviews')
                ->with('notice', [
                    'message' => trans('notice.review_deleted'),
                    'status'  => 'success'
                ]);
        }
    }
