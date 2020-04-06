<?php

    namespace App\Http\Controllers\Oleus;

    use App\FileImage;
    use App\Http\Controllers\Controller;
    use App\Library\Conf;
    use App\Library\Dashboard;
    use App\Models\MetaTags;
    use App\Models\Settings;
    use App\Models\ShopProduct;
    use App\Models\Variables;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Larapack\ConfigWriter\Facade as Config;
    use Validator;

    class SettingsController extends Controller
    {
        use Dashboard;

        public function __construct()
        {
            parent::__construct();
        }

        public function _view(Request $request, $method)
        {
            if(method_exists($this, $method)) {
                return $this->callAction($method, [$request]);
            }

            return redirect()
                ->back()
                ->with('notice', [
                    'message' => trans('notice.page_not_found'),
                    'status'  => 'warning'
                ]);
        }

        public function _translate(Request $request, $method)
        {
            $method = "translate_{$method}";
            if(method_exists($this, $method)) {
                return $this->callAction($method, [$request]);
            }

            return response([
                [
                    'command' => 'swal',
                    'options' => [
                        'title'             => trans('notice.error_while_submitting_form_title'),
                        'html'              => trans('notice.error_while_submitting_form_text'),
                        'type'              => 'error',
                        'showCloseButton'   => TRUE,
                        'showConfirmButton' => FALSE,
                        'timer'             => 5000
                    ]
                ]
            ], 200);
        }

        public function seo(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_seo'),
                'seo._title'  => trans('pages.settings_seo')
            ]);
            $item = wrap()->get('variables.seo');
            $_default_locale = config('app.locale');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'seo';
                $form->seo = FALSE;
                $form->tabs = [
                    [
                        'title'   => trans('others.tab_settings'),
                        'content' => [
                            field_render("settings.{$_default_locale}.title", [
                                'label' => 'Title' . ' (' . trans('others.default') . ')',
                                'value' => $item['settings'][$_default_locale]['title'],
                            ]),
                            field_render("settings.{$_default_locale}.description", [
                                'type'       => 'textarea',
                                'label'      => 'Description' . ' (' . trans('others.default') . ')',
                                'value'      => $item['settings'][$_default_locale]['description'],
                                'attributes' => [
                                    'rows' => 5,
                                ]
                            ]),
                            field_render("settings.{$_default_locale}.keywords", [
                                'type'       => 'textarea',
                                'label'      => 'Keywords' . ' (' . trans('others.default') . ')',
                                'value'      => $item['settings'][$_default_locale]['keywords'],
                                'attributes' => [
                                    'rows' => 5,
                                ]
                            ]),
                            field_render('robots', [
                                'type'   => 'select',
                                'label'  => 'Robots',
                                'value'  => $item['robots'],
                                'values' => [
                                    'index, follow'     => 'index, follow',
                                    'noindex, follow'   => 'noindex, follow',
                                    'index, nofollow'   => 'index, nofollow',
                                    'noindex, nofollow' => 'noindex, nofollow'
                                ],
                                'class'  => 'uk-select2'
                            ]),
                            field_render("settings.{$_default_locale}.suffix_title", [
                                'label' => trans('others.suffix_title'),
                                'value' => $item['settings'][$_default_locale]['suffix_title'],
                            ]),
                            field_render("settings.{$_default_locale}.copyright", [
                                'label' => trans('others.copyright_head'),
                                'value' => $item['settings'][$_default_locale]['copyright'],
                            ]),
                            '<hr class="uk-divider-icon">',
                            field_render('path.absolute', [
                                'type'     => 'checkbox',
                                'label'    => trans('forms.label_path_absolute'),
                                'selected' => $item['path']['absolute'] ? 1 : 0,
                            ]),
                            field_render('path.secure', [
                                'type'     => 'checkbox',
                                'label'    => trans('forms.label_path_secure'),
                                'selected' => $item['path']['secure'] ? 1 : 0,
                            ]),
                            field_render('path.ending', [
                                'type'     => 'checkbox',
                                'label'    => trans('forms.label_path_ending'),
                                'selected' => $item['path']['ending'] ? 1 : 0,
                            ]),
                            field_render('last_modified', [
                                'type'     => 'checkbox',
                                'label'    => trans('forms.label_meta_last_modified'),
                                'selected' => $item['last_modified'] ? 1 : 0,
                            ]),
                            field_render('compress', [
                                'type'     => 'checkbox',
                                'label'    => trans('forms.label_compress_code_html'),
                                'selected' => $item['compress'] ? 1 : 0,
                            ]),
                        ]
                    ],
                    [
                        'title'   => trans('others.tab_analytics'),
                        'content' => [
                            field_render("analytics.google", [
                                'label' => 'Google Analytics',
                                'value' => $item['analytics']['google'],
                            ]),
                            field_render("analytics.facebook", [
                                'label' => 'Facebook Pixel',
                                'value' => $item['analytics']['facebook'],
                            ]),
                        ]
                    ],
                    [
                        'title'   => 'ROBOTS.TXT',
                        'content' => [
                            field_render('robots_txt', [
                                'type'       => 'textarea',
                                'label'      => 'robots.txt',
                                'value'      => $item['robots_txt'],
                                'attributes' => [
                                    'rows' => 20,
                                ]
                            ])
                        ]
                    ]
                ];

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'last_modified',
                'compress',
                'settings',
                'path',
                'robots_txt',
                'analytics'
            ]);
            $_config['path']['absolute'] = (bool)$_config['path']['absolute'];
            $_config['path']['secure'] = (bool)$_config['path']['secure'];
            $_config['path']['ending'] = (bool)$_config['path']['ending'];
            $_config['last_modified'] = (bool)$_config['last_modified'];
            $_config['compress'] = (bool)$_config['compress'];
            Conf::set('os_seo', array_dot($_config));

            return redirect()
                ->route('oleus.settings', ['page' => 'seo'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function overall(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_overall'),
                'seo._title'  => trans('pages.settings_overall')
            ]);
            $item = wrap()->get('variables.seo');
            $_default_locale = config('app.locale');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'overall';
                $form->seo = FALSE;
                $form->tabs = [
                    [
                        'title'   => trans('others.tab_settings'),
                        'content' => [
                            field_render("settings.{$_default_locale}.site_name", [
                                'label' => trans('forms.label_site_name'),
                                'value' => $item['settings'][$_default_locale]['site_name'],
                            ]),
                            field_render("settings.{$_default_locale}.site_slogan", [
                                'type'       => 'textarea',
                                'label'      => trans('forms.label_site_slogan'),
                                'value'      => $item['settings'][$_default_locale]['site_slogan'],
                                'attributes' => [
                                    'rows' => 2,
                                ]
                            ]),
                            field_render("settings.{$_default_locale}.site_copyright", [
                                'label' => trans('forms.label_site_copyright'),
                                'value' => $item['settings'][$_default_locale]['site_copyright'],
                                'help'  => trans('forms.help_site_copyright')
                            ]),
                            '<hr class="uk-divider-icon">',
                            field_render('theme_color', [
                                'type'  => 'color',
                                'label' => trans('forms.label_style_theme_color'),
                                'icon'  => 'paint-bucket',
                                'value' => $item['theme_color'],
                            ]),
                            field_render('page_class', [
                                'label' => trans('forms.label_style_body_class'),
                                'value' => $item['page_class'],
                            ]),
                            field_render('favicon', [
                                'type'   => 'file',
                                'label'  => 'Favicon',
                                'allow'  => 'jpg|jpeg|gif|png|ico',
                                'values' => $item['favicon'] ? [f_get($item['favicon'])] : NULL,
                            ]),
                            '<hr class="uk-divider-icon">',
                            field_render('logotype.first', [
                                'type'   => 'file',
                                'label'  => trans('forms.label_logotype', ['logotype' => '&lt;header&gt;']),
                                'allow'  => 'jpg|jpeg|gif|png|svg',
                                'values' => $item['logotype']['first'] ? [f_get($item['logotype']['first'])] : NULL,
                            ]),
                            field_render('logotype.last', [
                                'type'   => 'file',
                                'label'  => trans('forms.label_logotype', ['logotype' => '&lt;footer&gt;']),
                                'allow'  => 'jpg|jpeg|gif|png|svg',
                                'values' => $item['logotype']['last'] ? [f_get($item['logotype']['last'])] : NULL,
                            ]),
							  field_render('logotype.next', [
                                'type'   => 'file',
                                'label'  => trans('forms.label_logotype', ['logotype' => '&lt;mobile&gt;']),
                                'allow'  => 'jpg|jpeg|gif|png|svg',
                                'values' => $item['logotype']['next'] ? [f_get($item['logotype']['next'])] : NULL,
                            ]),
							 field_render('logotype.modal', [
                                'type'   => 'file',
                                'label'  => trans('forms.label_logotype', ['logotype' => '&lt;modal&gt;']),
                                'allow'  => 'jpg|jpeg|gif|png|svg',
                                'values' => $item['logotype']['modal'] ? [f_get($item['logotype']['modal'])] : NULL,
                            ]),
                        ]
                    ]
                ];

                return view($form->theme, compact('form', 'item'));
            }
            if($favicon = $request->input('favicon')) {
                $_favicon = array_shift($favicon);
                Session::flash('favicon', json_encode([f_get($_favicon['id'])]));
            }
            if($logotype_first = $request->input('logotype.first')) {
                $_logotype_first = array_shift($logotype_first);
                Session::flash('logotype.first', json_encode([f_get($_logotype_first['id'])]));
            }
            if($logotype_last = $request->input('logotype.last')) {
                $_logotype_last = array_shift($logotype_last);
                Session::flash('logotype.last', json_encode([f_get($_logotype_last['id'])]));
            }
			 if($logotype_next = $request->input('logotype.next')) {
                $_logotype_next = array_shift($logotype_next);
                Session::flash('logotype.next', json_encode([f_get($_logotype_next['id'])]));
            }
			 if($logotype_modal = $request->input('logotype.modal')) {
                $_logotype_modal = array_shift($logotype_modal);
                Session::flash('logotype.modal', json_encode([f_get($_logotype_modal['id'])]));
            }
            $_config = $request->only([
                'settings',
                'theme_color',
                'favicon',
                'logotype',
            ]);
            if(isset($_favicon)) {
                $_config['favicon'] = (int)$_favicon['id'];
            }
            if(isset($_logotype_first)) {
                $_config['logotype']['first'] = (int)$_logotype_first['id'];
            }
            if(isset($_logotype_last)) {
                $_config['logotype']['last'] = (int)$_logotype_last['id'];
            }
			  if(isset($_logotype_next)) {
                $_config['logotype']['next'] = (int)$_logotype_next['id'];
            }
			if(isset($_logotype_modal)) {
                $_config['logotype']['modal'] = (int)$_logotype_modal['id'];
            }
            Conf::set('os_seo', array_dot($_config));
            Session::forget([
                'logotype.first',
                'logotype.last',
				'logotype.next',
				'logotype.modal',
                'favicon'
            ]);

            return redirect()
                ->route('oleus.settings', ['page' => 'overall'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function contacts(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_contacts'),
                'seo._title'  => trans('pages.settings_contacts')
            ]);
            $item = wrap()->get('variables.contacts');
            $_default_locale = DEFAULT_LANGUAGE;
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'contacts';
                $form->seo = FALSE;
                $_cities = NULL;
                foreach($item['cities'] as $_city_id => $_city) {
                    if(!is_null($_cities)) {
                        $_cities[] = '<hr class="uk-divider-icon">';
                    }
                    $_cities[] = field_render("cities.{$_city_id}.{$_default_locale}.city", [
                        'label' => trans('forms.label_city_name'),
                        'value' => $_city[$_default_locale]['city'],
                    ]);
                    //                    $_cities[] = field_render("cities.{$_city_id}.{$_default_locale}.suffix_alias", [
                    //                        'label' => trans('forms.label_suffix_alias'),
                    //                        'value' => $_city[$_default_locale]['suffix_alias'],
                    //                    ]);
                    if(count($item['cities']) > 1) {
                        $_cities[] = field_render('default_city', [
                            'type'     => 'radio',
                            'label'    => trans('others.default'),
                            'value'    => $_city_id,
                            'selected' => $item['default_city'],
                        ]);
                    }
                    $_offices = NULL;
                    if(count($_city[$_default_locale]['offices'])) {
                        $_offices .= '<h3 class="uk-heading-bullet uk-margin-small">' . trans('others.tab_offices') . '</h3>';
                        $_office_index = 0;
                        foreach($_city[$_default_locale]['offices'] as $_office_id => $_office) {
                            $_office_index++;
                            $_offices .= '<div class="uk-form-row"><label class="uk-form-label">' . trans('forms.label_office',
                                    ['office' => $_office_index]) . '</label><div class="uk-form-controls">';
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.address",
                                [
                                    'value'      => $_office['address'],
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_address')
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.work_time_weekdays",
                                [
                                    'value'      => $_office['work_time_weekdays'],
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_work_time_weekdays')
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.work_time_saturday",
                                [
                                    'value'      => $_office['work_time_saturday'],
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_work_time_saturday')
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.work_time_sunday",
                                [
                                    'value'      => $_office['work_time_sunday'],
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_work_time_sunday')
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.email", [
                                'value'      => $_office['email'],
                                'attributes' => [
                                    'placeholder' => trans('forms.label_office_email')
                                ]
                            ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.skype", [
                                'value'      => $_office['skype'],
                                'attributes' => [
                                    'placeholder' => 'Skype'
                                ]
                            ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.viber", [
                                'value'      => $_office['viber'],
                                'attributes' => [
                                    'placeholder' => 'Viber'
                                ]
                            ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.whatsapp",
                                [
                                    'value'      => $_office['whatsapp'],
                                    'attributes' => [
                                        'placeholder' => 'WhatsApp'
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.telegram",
                                [
                                    'value'      => $_office['telegram'],
                                    'attributes' => [
                                        'placeholder' => 'Telegram'
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.phones.phone_1",
                                [
                                    'value'      => $_office['phones']['phone_1'],
                                    'class'      => 'uk-phone-mask',
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_phone', ['phone' => 1]),
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.phones.phone_2",
                                [
                                    'value'      => $_office['phones']['phone_2'],
                                    'class'      => 'uk-phone-mask',
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_phone', ['phone' => 2]),
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.phones.phone_3",
                                [
                                    'value'      => $_office['phones']['phone_3'],
                                    'class'      => 'uk-phone-mask',
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_office_phone', ['phone' => 3]),
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.lat",
                                [
                                    'value'      => $_office['lat'],
                                    'attributes' => [
                                        'placeholder' => 'Широта',
                                    ]
                                ]);
                            $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.offices.{$_office_id}.lon",
                                [
                                    'value'      => $_office['lon'],
                                    'attributes' => [
                                        'placeholder' => 'Долгота',
                                    ]
                                ]);
                            if(count($_city[$_default_locale]['offices']) > 1) {
                                $_offices .= field_render("cities.{$_city_id}.{$_default_locale}.default_office", [
                                    'type'     => 'radio',
                                    'label'    => trans('others.default'),
                                    'value'    => $_office_id,
                                    'selected' => $_city[$_default_locale]['default_office'],
                                ]);
                            }
                            $_offices .= '</div></div>';
                        }
                        $_cities[] = $_offices;
                    }
                }
                $form->tabs[] = [
                    'title'   => trans('others.tab_contact'),
                    'content' => $_cities
                ];
                $_socials = [
                    'title'   => trans('others.tab_social'),
                    'content' => []
                ];
                foreach($item['social'] as $_key => $_social) {
                    array_push($_socials['content'], field_render("social.{$_key}", [
                        'label' => trans("forms.label_social_{$_key}"),
                        'value' => $_social,
                    ]));
                }
                $form->tabs[] = $_socials;

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'default_city',
                'social',
                'cities'
            ]);
            if(!isset($_config['default_city'])) {
                $_config['default_city'] = key($request->get('cities'));
            }
            $_config['several_location'] = count($_config['cities']) > 1 ? TRUE : FALSE;
            Conf::set('os_contacts', array_dot($_config));
            Config::write('app', ['location' => $_config['default_city']]);

            return redirect()
                ->route('oleus.settings', ['page' => 'contacts'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function languages(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_languages'),
                'seo._title'  => trans('pages.settings_languages')
            ]);
            $item = wrap()->get('variables.i18n');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'languages';
                $form->seo = FALSE;
                $form->translate = FALSE;
                if(count($item['languages']) > 1) {
                    $_languages[] = field_render('multi_language', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_i18n_use'),
                        'selected' => $item['multi_language']
                    ]);
                    $_languages[] = '<hr class="uk-divider-icon">';
                }
                foreach($item['languages'] as $_language_id => $_language) {
                    $_languages[] = '<h3 class="uk-heading-bullet uk-margin-small">' . $_language['full_name'] . '</h3>';
                    $_languages[] = field_render("languages.{$_language_id}.full_name", [
                        'label' => trans('forms.label_language_full_name'),
                        'value' => $_language['full_name'],
                    ]);
                    $_languages[] = field_render("languages.{$_language_id}.short_name", [
                        'label' => trans('forms.label_language_short_name'),
                        'value' => $_language['short_name'],
                    ]);
                    $_languages[] = field_render('locale', [
                        'type'     => 'radio',
                        'values'   => [
                            $_language_id => trans('others.default')
                        ],
                        'selected' => config('app.locale'),
                    ]);
                }
                $form->tabs[] = [
                    'title'   => trans('others.tab_language'),
                    'content' => $_languages
                ];

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'languages',
                'multi_language',
                'locale'
            ]);
            $_config['multi_language'] = $_config['multi_language'] ? TRUE : FALSE;
            Conf::set('os_languages', array_dot($_config));
            Config::write('app', ['locale' => $_config['locale']]);

            return redirect()
                ->route('oleus.settings', ['page' => 'languages'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function currencies(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_currencies'),
                'seo._title'  => trans('pages.settings_currencies')
            ]);
            $item = wrap()->get('variables.currencies');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'currencies';
                $form->seo = FALSE;
                $form->translate = FALSE;
                $form->additional_buttons = [
                    _l('Добавить валюту', 'oleus.modal.settings', [
                        'p' => ['setting' => 'currencies'],
                        'a' => ['class' => ['uk-button uk-button-primary uk-waves uk-border-rounded use-ajax']]
                    ])
                ];
                if(count($item['currencies']) > 1) {
                    $_currencies[] = field_render('multi_currency', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_currencies_use'),
                        'selected' => $item['multi_currency'] ? 1 : 0,
                    ]);

                    $_currencies[] = field_render('view_mode', [
                        'type'  => 'hidden',
                        'value' => 0,
                    ]);
                    $_currencies[] = '<hr class="uk-divider-icon">';
                }
                foreach($item['currencies'] as $_currency_code => $_currency) {
                    $_currencies[] = '<h3 class="uk-heading-bullet uk-margin-small">' . $_currency['full_name'] . '</h3>';
                    $_currencies[] = field_render("currencies.{$_currency_code}.not_remove", [
                        'type'  => 'hidden',
                        'value' => $_currency['not_remove'],
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.full_name", [
                        'label' => trans('forms.label_full_name'),
                        'value' => $_currency['full_name'],
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.iso_code", [
                        'label' => 'ISO 4217',
                        'help'  => 'Трехзначный буквенный код согласно стандарту ISO 4217',
                        'value' => $_currency['iso_code'],
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.prefix", [
                        'label' => trans('forms.label_text_prefix'),
                        'value' => $_currency['prefix'],
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.suffix", [
                        'label' => trans('forms.label_text_suffix'),
                        'value' => $_currency['suffix'],
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.ratio", [
                        'type'       => 'number',
                        'label'      => trans('forms.label_currencies_ratio'),
                        'value'      => $_currency['ratio'],
                        'attributes' => [
                            'min'  => 0,
                            'step' => 0.001
                        ]
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.precision", [
                        'type'   => 'select',
                        'label'  => trans('forms.label_currencies_precision'),
                        'value'  => $_currency['precision'],
                        'values' => [
                            0 => plural_string(0, [
                                trans('others.plural_sing'),
                                trans('others.plural_sings'),
                                trans('others.plural_sings2')
                            ]),
                            1 => plural_string(1, [
                                trans('others.plural_sing'),
                                trans('others.plural_sings'),
                                trans('others.plural_sings2')
                            ]),
                            2 => plural_string(2, [
                                trans('others.plural_sing'),
                                trans('others.plural_sings'),
                                trans('others.plural_sings2')
                            ]),
                        ],
                        'class'  => 'uk-select2'
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.precision_mode", [
                        'type'   => 'select',
                        'label'  => trans('forms.label_currencies_precision_mode'),
                        'value'  => $_currency['precision_mode'],
                        'values' => [
                            0 => trans('forms.value_precision_mode_0'),
                            1 => trans('forms.value_precision_mode_1'),
                            2 => trans('forms.value_precision_mode_2'),
                            3 => trans('forms.value_precision_mode_3')
                        ],
                        'class'  => 'uk-select2',
                        'help'   => trans('forms.help_currencies_precision_mode')
                    ]);
                    $_currencies[] = field_render("currencies.{$_currency_code}.use", [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.value_use'),
                        'selected' => $_currency['use'],
                    ]);
                    $_currencies[] = field_render('default_currency', [
                        'type'     => 'radio',
                        'values'   => [
                            $_currency_code => trans('others.default')
                        ],
                        'selected' => $item['default_currency'],
                    ]);
                    if(!$_currency['not_remove']) {
                        $_currencies[] = field_render("currencies.{$_currency_code}.remove", [
                            'type'  => 'checkbox',
                            'label' => trans('forms.button_delete'),
                            'class' => 'uk-text-danger uk-text-uppercase uk-text-bold'
                        ]);
                    }
                    $_currencies[] = '<hr>';
                }
                array_pop($_currencies);
                $form->tabs[] = [
                    'title'   => trans('others.tab_currencies'),
                    'content' => $_currencies
                ];

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'multi_currency',
                'currencies',
                'default_currency',
                'precision_mode',
                'precision'
            ]);
            $_rebuild = FALSE;
            foreach($_config['currencies'] as $_iso_code => &$_currency) {
                if(isset($_currency['remove']) && $_currency['remove']) {
                    unset($_config['currencies'][$_iso_code]);
                    $_rebuild = TRUE;
                    ShopProduct::where('currency', $_iso_code)
                        ->update([
                            'currency' => DEFAULT_CURRENCY
                        ]);
                    continue;
                }
                $_currency['use'] = $_currency['use'] ? TRUE : FALSE;
                $_currency['ratio'] = $_currency['ratio'] && $_currency['ratio'] != 1 ? round($_currency['ratio'], 3) : 1;
            }
            $_config['multi_currency'] = $_config['multi_currency'] ? TRUE : FALSE;
            Conf::set('os_currency', array_dot($_config), $_rebuild);
            $_shop_products = ShopProduct::all();
            if($_shop_products->isNotEmpty()) {
                $_shop_products->each(function ($_product) use ($_config) {
                    if($_product->currency != $_config['default_currency']) {
                        $_product->base_price = transform_price($_product->price, $_product->currency, $_config)['format']['price'];
                        $_product->save();
                    }
                });
            }

            return redirect()
                ->route('oleus.settings', ['page' => 'currencies'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function modal_currencies(Request $request)
        {
            $commands = [];
            if($request->has('forms')) {
                $validate_rules = [
                    'currencies.full_name' => 'required',
                    'currencies.iso_code'  => 'required|min:3|max:3'
                ];
                $validator = Validator::make($request->all(), $validate_rules);
                foreach($validate_rules as $field => $rule) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => '#form-field-' . str_slug(str_replace('.', '-', $field), '-'),
                        'data'    => 'uk-form-danger'
                    ];
                }
                if($validator->fails()) {
                    foreach($validator->errors()->messages() as $field => $message) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => '#form-field-' . str_slug(str_replace('.', '-', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'danger',
                        'text'    => trans('notice.errors')
                    ];
                } else {
                    $_config = config('os_currency');
                    $_save = $request->get('currencies');
                    if(isset($_config['currencies'][mb_strtolower($_save['iso_code'])])) {
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'danger',
                            'text'    => 'Валюта с таким кодом "<strong>ISO 4217</strong>" уже добавлена'
                        ];
                    } else {
                        if(!$_save['ratio']) $_save['ratio'] = 1;
                        $_save['key'] = mb_strtolower($_save['iso_code']);
                        $_save['not_remove'] = 0;
                        $_config['currencies'][mb_strtolower($_save['iso_code'])] = $_save;
                        Conf::set('os_currency', array_dot($_config));
                        $commands[] = [
                            'command' => 'modal_close',
                            'target'  => '#modals-form-add-new-currencies'
                        ];
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => 'Валюта добавлена'
                        ];
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => 'Перезагрузите страницу для отображения ее в списке'
                        ];
                    }
                }
            } else {
                $form = parent::__form();
                $form->title = trans('Добавить новую валюту');
                $form->route = _r('oleus.modal.settings', ['setting' => 'currencies']);
                $form->tabs[] = field_render('forms', [
                    'type'  => 'hidden',
                    'value' => 'add-new',
                ]);
                $form->tabs[] = field_render('currencies.full_name', [
                    'label'    => trans('forms.label_full_name'),
                    'required' => TRUE
                ]);
                $form->tabs[] = field_render('currencies.iso_code', [
                    'label'    => 'ISO 4217',
                    'help'     => 'Трехзначный буквенный код согласно стандарту ISO 4217',
                    'required' => TRUE
                ]);
                $form->tabs[] = field_render('currencies.prefix', [
                    'label' => trans('forms.label_text_prefix')
                ]);
                $form->tabs[] = field_render('currencies.suffix', [
                    'label' => trans('forms.label_text_suffix')
                ]);
                $form->tabs[] = field_render('currencies.ratio', [
                    'type'       => 'number',
                    'label'      => trans('forms.label_currencies_ratio'),
                    'attributes' => [
                        'min'  => 0,
                        'step' => 0.001
                    ],
                    'help'       => 'Если не указан, то воспринимается как 1',
                ]);
                $form->tabs[] = field_render("currencies.precision", [
                    'type'   => 'select',
                    'label'  => trans('forms.label_currencies_precision'),
                    'values' => [
                        0 => plural_string(0, [
                            trans('others.plural_sing'),
                            trans('others.plural_sings'),
                            trans('others.plural_sings2')
                        ]),
                        1 => plural_string(1, [
                            trans('others.plural_sing'),
                            trans('others.plural_sings'),
                            trans('others.plural_sings2')
                        ]),
                        2 => plural_string(2, [
                            trans('others.plural_sing'),
                            trans('others.plural_sings'),
                            trans('others.plural_sings2')
                        ]),
                    ],
                    'class'  => 'uk-select2'
                ]);
                $form->tabs[] = field_render("currencies.precision_mode", [
                    'type'   => 'select',
                    'label'  => trans('forms.label_currencies_precision_mode'),
                    'values' => [
                        0 => trans('forms.value_precision_mode_0'),
                        1 => trans('forms.value_precision_mode_1'),
                        2 => trans('forms.value_precision_mode_2'),
                        3 => trans('forms.value_precision_mode_3')
                    ],
                    'class'  => 'uk-select2',
                    'help'   => trans('forms.help_currencies_precision_mode')
                ]);
                $form->tabs[] = field_render('currencies.use', [
                    'type'  => 'checkbox',
                    'label' => trans('forms.value_use'),
                ]);
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-add-new-currencies',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function delivery(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_delivery'),
                'seo._title'  => trans('pages.settings_delivery')
            ]);
            $item = wrap()->get('variables.shop');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'delivery';
                $form->seo = FALSE;
                $form->translate = FALSE;
                $_deliveries[] = '<div class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom"><span uk-icon="icon: ui_info; ratio: .8" class="uk-margin-small-right uk-text-primary"></span>' . trans('forms.help_delivery') . '</div>';
                $i = 0;
                foreach($item['deliveries'] as $_delivery_code => $_delivery) {
                    if($i != 0) $_deliveries[] = '<hr>';
                    $i++;
                    $_deliveries[] = field_render("deliveries.{$_delivery_code}.use", [
                        'type'     => 'checkbox',
                        'label'    => $_delivery['name'],
                        'selected' => $_delivery['use'],
                    ]);
                    $_deliveries[] = field_render("deliveries.{$_delivery_code}.placeholder", [
                        'selected'   => $_delivery['placeholder'],
                        'attributes' => [
                            'placeholder' => 'Текст описания для способа доставки'
                        ]
                    ]);
                    $_deliveries[] = field_render("deliveries.{$_delivery_code}.use_of_data", [
                        'type'     => 'checkbox',
                        'label'    => 'Поле ввода',
                        'selected' => $_delivery['use_of_data'],
                    ]);
                }
                $form->tabs[] = [
                    'title'   => trans('others.tab_delivery'),
                    'content' => $_deliveries
                ];
                $form->tabs[] = [
                    'title'   => 'Нова Пошта',
                    'content' => [
                        field_render('np.key', [
                            'label'      => 'Ключ API "Нова Пошта"',
                            'value'      => $item['np']['key'] ?? NULL,
                        ])
                    ]
                ];

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'deliveries',
                'np'
            ]);
            Conf::set('os_shop', array_dot($_config));

            return redirect()
                ->route('oleus.settings', ['page' => 'delivery'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function payment(Request $request)
        {
            $this->set_wrap([
                'page._title' => trans('pages.settings_payment'),
                'seo._title'  => trans('pages.settings_payment')
            ]);
            $item = wrap()->get('variables.shop');
            if($request->method() == 'GET') {
                $form = parent::__form();
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route_tag = 'payment';
                $form->seo = FALSE;
                $form->translate = FALSE;
                $_payments[] = '<div class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom"><span uk-icon="icon: ui_info; ratio: .8" class="uk-margin-small-right uk-text-primary"></span>' . trans('forms.help_payment') . '</div>';
                $i = 0;
                foreach($item['payments'] as $_delivery_code => $_payment) {
                    $_payments[] = field_render("payments.{$_delivery_code}.use", [
                        'type'     => 'checkbox',
                        'label'    => $_payment['name'],
                        'selected' => $_payment['use'],
                    ]);
                }
                $_payments[] = '<hr class="uk-divider-icon">';
                $_payments[] = '<h2 class="uk-margin-remove">LiqPay</h2>';
                $_payments[] = field_render('liqPay.public_key', [
                    'label' => 'Public key',
                    'value' => $item['liqPay']['public_key'],
                ]);
                $_payments[] = field_render('liqPay.private_key', [
                    'label' => 'Private key',
                    'value' => $item['liqPay']['private_key'],
                ]);
                $form->tabs[] = [
                    'title'   => trans('others.tab_payment'),
                    'content' => $_payments
                ];

                return view($form->theme, compact('form', 'item'));
            }
            $_config = $request->only([
                'payments',
                'liqPay'
            ]);
            Conf::set('os_shop', array_dot($_config));

            return redirect()
                ->route('oleus.settings', ['page' => 'payment'])
                ->with('notice', [
                    'message' => trans('notice.settings_save'),
                    'status'  => 'success'
                ]);
        }

        public function translate_overall(Request $request)
        {
            if($request->has('forms')) {
                $_config = $request->only([
                    'settings'
                ]);
                Conf::set('os_seo', array_dot($_config));
                $commands[] = [
                    'command' => 'modal_close',
                    'target'  => '#modals-form-translate-overall'
                ];
                $commands[] = [
                    'command' => 'notice',
                    'status'  => 'success',
                    'text'    => trans('notice.translate_save')
                ];
            } else {
                $item = config('os_seo');
                $_languages = config('os_languages.languages');
                $_default_locale = config('app.locale');
                $form = parent::__form();
                $form->title = trans('forms.label_i18n_translate_data');
                $form->route = _r('oleus.settings.translate', ['page' => 'overall']);
                foreach($_languages as $_lang_key => $_lang_value) {
                    if($_lang_key != $_default_locale) {
                        $form->tabs[] = "<h3 class='uk-heading-bullet uk-margin-small-bottom'>{$_lang_value['full_name']}</h3>";
                        $form->tabs[] = field_render('forms', [
                            'type'  => 'hidden',
                            'value' => 'translate-overall',
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.site_name", [
                            'id'    => 'translate-overall-site-name',
                            'label' => trans('forms.label_site_name'),
                            'value' => $item['settings'][$_lang_key]['site_name'],
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.site_slogan", [
                            'id'         => 'translate-overall-site-slogan',
                            'type'       => 'textarea',
                            'label'      => trans('forms.label_site_slogan'),
                            'value'      => $item['settings'][$_lang_key]['site_slogan'],
                            'attributes' => [
                                'rows' => 2,
                            ]
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.site_copyright", [
                            'id'    => 'translate-overall-site-copyright',
                            'label' => trans('forms.label_site_copyright'),
                            'value' => $item['settings'][$_lang_key]['site_copyright'],
                            'help'  => trans('forms.help_site_copyright')
                        ]);
                    }
                }
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-translate-overall',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function translate_seo(Request $request)
        {
            if($request->has('forms')) {
                $_config = $request->only([
                    'settings'
                ]);
                Conf::set('os_seo', array_dot($_config));
                $commands[] = [
                    'command' => 'modal_close',
                    'target'  => '#modals-form-translate-seo'
                ];
                $commands[] = [
                    'command' => 'notice',
                    'status'  => 'success',
                    'text'    => trans('notice.translate_save')
                ];
            } else {
                $item = config('os_seo');
                $_languages = config('os_languages.languages');
                $_default_locale = config('app.locale');
                $form = parent::__form();
                $form->title = trans('forms.label_i18n_translate_data');
                $form->theme = 'oleus.base.forms.form_settings';
                $form->route = _r('oleus.settings.translate', ['page' => 'seo']);
                foreach($_languages as $_lang_key => $_lang_value) {
                    if($_lang_key != $_default_locale) {
                        $form->tabs[] = "<h3 class='uk-heading-bullet uk-margin-small-bottom'>{$_lang_value['full_name']}</h3>";
                        $form->tabs[] = field_render('forms', [
                            'type'  => 'hidden',
                            'value' => 'translate-seo',
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.title", [
                            'id'    => 'translate-seo-title',
                            'label' => 'Title' . ' (' . trans('others.default') . ')',
                            'value' => $item['settings'][$_lang_key]['title'],
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.description", [
                            'id'         => 'translate-seo-description',
                            'type'       => 'textarea',
                            'label'      => 'Description' . ' (' . trans('others.default') . ')',
                            'value'      => $item['settings'][$_lang_key]['description'],
                            'attributes' => [
                                'rows' => 3,
                            ]
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.keywords", [
                            'id'         => 'translate-seo-keywords',
                            'label'      => 'Keywords' . ' (' . trans('others.default') . ')',
                            'type'       => 'textarea',
                            'value'      => $item['settings'][$_lang_key]['keywords'],
                            'attributes' => [
                                'rows' => 3,
                            ]
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.suffix_title", [
                            'id'    => 'translate-seo-suffix-title',
                            'label' => trans('others.suffix_title'),
                            'value' => $item['settings'][$_lang_key]['suffix_title'],
                        ]);
                        $form->tabs[] = field_render("settings.{$_lang_key}.copyright", [
                            'id'    => 'translate-seo-copyright',
                            'label' => trans('others.copyright_head'),
                            'value' => $item['settings'][$_lang_key]['copyright'],
                        ]);
                    }
                }
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-translate-seo',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function translate_contacts(Request $request)
        {
            if($request->has('forms')) {
                $item = config('os_contacts');
                $_default_locale = config('app.locale');
                $_config = $request->only([
                    'cities'
                ]);
                foreach($_config['cities'] as $_city_id => $_city_language) {
                    $_offices = &$item['cities'][$_city_id][$_default_locale]['offices'];
                    foreach($_city_language as $_language_id => $_city) {
                        if(count($_offices)) {
                            foreach($_offices as $_office_id => &$_office) {
                                $_default_office = $item['cities'][$_city_id][$_default_locale]['offices'][$_office_id];
                                unset($_default_office['address']);
                                unset($_default_office['work_time_weekdays']);
                                unset($_default_office['work_time_saturday']);
                                unset($_default_office['work_time_sunday']);
                                $_config['cities'][$_city_id][$_language_id]['offices'][$_office_id] = array_merge_recursive($_config['cities'][$_city_id][$_language_id]['offices'][$_office_id],
                                    $_default_office);
                            }
                        }
                    }
                }
                Conf::set('os_contacts', array_dot($_config));
                $commands[] = [
                    'command' => 'modal_close',
                    'target'  => '#modals-form-translate-contacts'
                ];
                $commands[] = [
                    'command' => 'notice',
                    'status'  => 'success',
                    'text'    => trans('notice.translate_save')
                ];
            } else {
                $item = config('os_contacts');
                $_languages = config('os_languages.languages');
                $_default_locale = DEFAULT_LANGUAGE;
                $form = parent::__form();
                $form->title = trans('forms.label_i18n_translate_data');
                $form->route = _r('oleus.settings.translate', ['page' => 'contacts']);
                foreach($_languages as $_lang_key => $_lang_value) {
                    if($_lang_key != $_default_locale) {
                        $form->tabs[] = "<h3 class='uk-heading-bullet uk-margin-small-bottom'>{$_lang_value['full_name']}</h3>";
                        $form->tabs[] = field_render('forms', [
                            'type'  => 'hidden',
                            'value' => 'translate-contacts',
                        ]);
                        $_i = 1;
                        foreach($item['cities'] as $_city_id => $_city) {
                            $form->tabs[] = field_render("cities.{$_city_id}.{$_lang_key}.city", [
                                'id'    => 'translate-contacts-city',
                                'label' => trans('forms.label_city_name'),
                                'value' => $_city[$_lang_key]['city'] ? $_city[$_lang_key]['city'] : $_city[$_default_locale]['city'],
                            ]);
                            if(count($_city[$_lang_key]['offices'])) {
                                $_office_index = 0;
                                foreach($_city[$_lang_key]['offices'] as $_office_id => $_office) {
                                    $_office_index++;
                                    $form->tabs[] = field_render("cities.{$_city_id}.{$_lang_key}.offices.{$_office_id}.address",
                                        [
                                            'id'    => 'translate-contacts-address',
                                            'label' => trans('forms.label_office',
                                                    ['office' => $_office_index]) . ' (' . trans('forms.label_office_address') . ')',
                                            'value' => $_office['address'] ? $_office['address'] : $_city[$_default_locale]['offices'][$_office_id]['address']
                                        ]);
                                    $form->tabs[] = field_render("cities.{$_city_id}.{$_lang_key}.offices.{$_office_id}.work_time_weekdays",
                                        [
                                            'label' => trans('forms.label_office_work_time_weekdays'),
                                            'value' => $_office['work_time_weekdays'] ? $_office['work_time_weekdays'] : $_city[$_default_locale]['offices'][$_office_id]['work_time_weekdays']
                                        ]);
                                    $form->tabs[] = field_render("cities.{$_city_id}.{$_lang_key}.offices.{$_office_id}.work_time_saturday",
                                        [
                                            'label' => trans('forms.label_office_work_time_saturday'),
                                            'value' => $_office['work_time_saturday'] ? $_office['work_time_saturday'] : $_city[$_default_locale]['offices'][$_office_id]['work_time_saturday']
                                        ]);
                                    $form->tabs[] = field_render("cities.{$_city_id}.{$_lang_key}.offices.{$_office_id}.work_time_sunday",
                                        [
                                            'label' => trans('forms.work_time_sunday'),
                                            'value' => $_office['work_time_sunday'] ? $_office['work_time_sunday'] : $_city[$_default_locale]['offices'][$_office_id]['work_time_sunday']
                                        ]);
                                }
                            }
                            if(count($item['cities']) > 1 && count($item['cities']) != $_i) {
                                $form->tabs[] = '<hr>';
                                $_i++;
                            }
                        }
                    }
                }
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-translate-contacts',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }
    }
