<?php

    use App\Library\Fields;
    use App\Library\ImagePresets;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopProduct;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Storage;
    use Intervention\Image\Facades\Image;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

    /**
     * Page
     */
    if (!function_exists('page_render')) {
        function page_render($id, $language = DEFAULT_LANGUAGE)
        {
            if (is_numeric($id)) {
                $item = Page::related($id)
                    ->language($language)
                    ->location()
                    ->active()
                    ->with([
                        '_alias'
                    ])
                    ->first();
            } else {
                $item = Page::where('type', $id)
                    ->language($language)
                    ->location()
                    ->active()
                    ->with([
                        '_alias'
                    ])
                    ->first();
            }
            if ($item) $item->_render();

            return $item;
        }
    }

    /**
     * Node
     */
    if (!function_exists('node_render')) {
        function node_render($id, $language = DEFAULT_LANGUAGE)
        {
            $item = Node::related($id)
                ->language($language)
                ->location()
                ->with([
                    '_alias'
                ])
                ->first();
            if ($item) $item->_render();

            return $item;
        }
    }

    /**
     * Slider
     */
    if (!function_exists('slider_render')) {
        function slider_render($options)
        {
            $_options = array_merge([
                'entity'   => NULL,
                'theme'    => NULL,
                'language' => DEFAULT_LANGUAGE,
            ], $options);
            if (($item = slider_load($_options['entity'], $_options)) && $item->status) {
                return view($item->template, compact('item'))
                    ->render();
            }

            return NULL;
        }
    }

    /**
     * Attributes
     */
    if (!function_exists('render_attributes')) {
        function render_attributes($attributes = [])
        {
            if (!is_array($attributes) || !count($attributes)) return NULL;
            $_attributes = NULL;
            foreach ($attributes as $key => $attribute) {
                if (is_string($key) && !is_null($attribute) && !is_bool($attribute) && (is_string($attribute) || is_numeric($attribute) || is_float($attribute))) {
                    $_attributes[] = "{$key}=\"{$attribute}\"";
                } elseif (is_string($key) && (is_null($attribute) || (is_bool($attribute) && $attribute == TRUE))) {
                    $_attributes[] = $key;
                } elseif (!is_null($attribute) && !is_bool($attribute)) {
                    if ($attribute) $_attributes[] = $attribute;
                }
            }

            return $_attributes ? implode(' ', $_attributes) : NULL;
        }
    }

    /**
     * Field
     */
    if (!function_exists('field_render')) {
        function field_render($name, $variables = [])
        {
            $item = new Fields($name, $variables);

            return $item->_render();
        }
    }

    /**
     * Menu
     */
    if (!function_exists('menu_render')) {
        function menu_render($options = [])
        {
            $_options = array_merge([
                'entity' => NULL,
                'theme'  => NULL,
            ], $options);
            if ($_options['entity'] && ($item = menu_load($_options['entity'], $_options))) {
                if ($item->status) {
                    return view($item->template, compact('item'))
                        ->render();
                }
            }

            return NULL;
        }
    }

    /**
     * Breadcrumb
     */
    if (!function_exists('breadcrumb_render')) {
        function breadcrumb_render($options = [])
        {
            $_language = wrap()->get('locale');
            $_options = array_merge([
                'entity' => NULL,
            ], $options);
            $breadcrumb = collect([]);
            if ($_options['entity']) {
                $_position = 2;
                $_entity_class_basename = class_basename($_options['entity']);
                switch ($_entity_class_basename) {
                    case 'Page':
                        if ($_options['entity']->type != 'front') {
                            $breadcrumb->push([
                                'name'     => wrap()->get('page._title') ?? $_options['entity']->title,
                                'language' => $_options['entity']->language,
                                'url'      => NULL,
                                'position' => $_position
                            ]);
                        }
                        break;
                    case 'Node':
                        if ($_page = Page::find($_options['entity']->entity_id)) {
                            $_alias_page = $_page->_alias;
                            $breadcrumb->push([
                                'name'     => $_page->title,
                                'language' => $_page->language,
                                'url'      => $_alias_page->language != DEFAULT_LANGUAGE ? _u("{$_alias_page->language}/{$_alias_page->alias}") : _u($_alias_page->alias),
                                'position' => $_position
                            ]);
                        }
                        $_position++;
                        $breadcrumb->push([
                            'name'     => $_options['entity']->title,
                            'language' => $_options['entity']->language,
                            'url'      => NULL,
                            'position' => $_position
                        ]);
                        break;
                    case 'ShopCategory':
                        $_parents = $_options['entity']->parents;
                        if (is_array($_parents) && count($_parents)) {
                            foreach ($_parents as $_parent) {
                                $_category = shop_category_load($_parent->id, $_language);
                                $_alias_category = $_category->_alias;
                                $breadcrumb->push([
                                    'name'     => $_category->title,
                                    'language' => $_category->language,
                                    'url'      => $_alias_category->language != DEFAULT_LANGUAGE ? _u("{$_alias_category->language}/{$_alias_category->alias}") : _u($_alias_category->alias),
                                    'position' => $_position
                                ]);
                                $_position++;
                            }
                        }
                        $breadcrumb->push([
                            'name'     => wrap()->get('page._title') ?? $_options['entity']->title,
                            'language' => $_options['entity']->language,
                            'url'      => NULL,
                            'position' => $_position
                        ]);
                        break;
                    case 'ShopFilterParamsPage':
                        $_category = $_options['entity']->_category;
                        $_parents = $_category->parents;
                        if (is_array($_parents) && count($_parents)) {
                            foreach ($_parents as $_parent) {
                                $breadcrumb->push([
                                    'name'     => $_parent->title,
                                    'language' => $_parent->language,
                                    'url'      => $_parent->_alias ? _u($_parent->_alias->alias, [], TRUE) : NULL
                                ]);
                            }
                            $breadcrumb->push([
                                'name'     => $_category->title,
                                'language' => $_category->language,
                                'url'      => $_category->_alias ? _u($_category->_alias->alias, [], TRUE) : NULL
                            ]);
                        }
                        $breadcrumb->push([
                            'name'     => wrap()->get('page._title') ?? $_options['entity']->title,
                            'language' => $_options['entity']->language,
                            'url'      => NULL
                        ]);
                        break;
                    case 'ShopProduct':
                        if ($_category = $_options['entity']->category) {
                            $_parents = $_category->parents;
                            if (is_array($_parents) && count($_parents)) {
                                foreach ($_parents as $_parent) {
                                    $_category_parent = shop_category_load($_parent->id, $_language);
                                    $_alias_category = $_category_parent->_alias;
                                    $breadcrumb->push([
                                        'name'     => $_category_parent->title,
                                        'language' => $_category_parent->language,
                                        'url'      => $_alias_category->language != DEFAULT_LANGUAGE ? _u("{$_alias_category->language}/{$_alias_category->alias}") : _u($_alias_category->alias),
                                        'position' => $_position
                                    ]);
                                    $_position++;
                                }
                            }
                            $_alias_category = $_category->_alias;

                            $breadcrumb->push([
                                'name'     => $_category->title,
                                'language' => $_category->language,
                                'url'      => $_alias_category->language != DEFAULT_LANGUAGE ? _u("{$_alias_category->language}/{$_alias_category->alias}") : _u($_alias_category->alias),
                                'position' => $_position
                            ]);

                        }
                        $_position++;
                    //                        $breadcrumb->push([
                    //                           'name'     => wrap()->get('page._title') ?? $_options['entity']->title,
                    //                            'language' => $_options['entity']->language,
                    //                            'url'      => NULL,
                    //                            'position' => $_position
                    //                        ]);
                    //                        break;
                }
            }
            if ($breadcrumb->isNotEmpty()) {
                $breadcrumb->prepend([
                    'name'     => trans('front.home_page'),
                    'language' => $_options['entity']->language,
                    'url'      => _u('/', [], TRUE),
                ]);
            } else {
                $breadcrumb = NULL;
            }

            return $breadcrumb;
        }
    }

    /**
     * Shop category
     */
    if (!function_exists('shop_category_render')) {
        function shop_category_render($id, $language = DEFAULT_LANGUAGE)
        {
            $item = ShopCategory::related($id)
                ->language($language)
                ->location()
                ->with([
                    '_alias',
                    //                    '_filter_param_options',
                    //                    '_category_params',
                ])
                ->remember(15)
                ->first();
            if ($item) $item->_render();

            return $item;
        }
    }

    /**
     * Shop filter params page
     */
    if (!function_exists('shop_filter_params_render')) {
        function shop_filter_params_render($id, $language = DEFAULT_LANGUAGE)
        {
            $item = ShopFilterParamsPage::where('id', $id)
                ->with([
                    '_alias',
                    '_category'
                ])
                ->remember(15)
                ->first();
            if ($item) $item->_render();

            return $item;
        }
    }

    /**
     * Shop product
     */
    if (!function_exists('shop_product_render')) {
        function shop_product_render($id, $language = DEFAULT_LANGUAGE)
        {
            $item = ShopProduct::related($id)
                ->language($language)
                ->location()
                ->with([
                    '_alias',
                    '_preview',
                    '_background',
                    '_discount_timer'
                ])
                ->remember(15)
                ->first();
            if ($item) $item->_render();

            return $item;
        }
    }


    /******
     *
     */


    if (!function_exists('block_render')) {
        /**
         * @param      $id
         * @param null $theme
         * @return null|string
         * @throws Throwable
         */
        function block_render($id, $theme = NULL)
        {
            if ($item = block_load($id, $theme)) {
                if ($item->status) {
                    return view($item->theme, compact('item'))
                        ->render();
                }
            }

            return NULL;
        }
    }

    if (!function_exists('advantage_render')) {
        /**
         * @param $options
         * @return null|string
         * @throws Throwable
         */
        function advantage_render($options)
        {
            $_options = array_merge([
                'entity' => NULL,
                'theme'  => NULL,
            ], $options);
            if ($_options['entity'] && $item = advantage_load($_options['entity'], $_options['theme'])) {
                if ($item->status) {
                    return view($item->theme, compact('item'))
                        ->render();
                }
            }

            return NULL;
        }
    }

    if (!function_exists('banner_render')) {
        /**
         * @param $options
         * @return null|string
         * @throws Throwable
         */
        function banner_render($options)
        {
            $_options = array_merge([
                'entity' => NULL,
                'theme'  => NULL,
            ], $options);
            if ($item = banner_load($_options['entity'], $_options['theme'])) {
                if ($item->status) {
                    return view($item->theme, compact('item'))
                        ->render();
                }
            }

            return NULL;
        }
    }

    if (!function_exists('service_order_button')) {
        function service_order_button($name_button = NULL, $class = NULL)
        {
            $name_button = $name_button ? $name_button : trans('forms.button_service_order');
            $path_button = _r('ajax.service_order.form');

            return "<button type=\"button\" data-path=\"{$path_button}\" class=\"use-ajax uk-button uk-button-secondary uk-border-rounded {$class}\">{$name_button}</button>";
        }
    }

    if (!function_exists('callback_button')) {
        function callback_button($options = [])
        {
            $_options = array_merge([
                'title'     => trans('forms.button_callback_application'),
                'class'     => NULL,
                'id'        => NULL,
                'type'      => 'button',
                'data-path' => _r('ajax.callback.form'),
                'data-type' => 0,
                'uk-tooltip'
            ], $options);
            $_options['class'] .= ' use-ajax uk-button';

            return '<button ' . render_attributes($_options) . '>' . $_options['title'] . '</button>';
        }
    }

    if (!function_exists('complaint_button')) {
        function complaint_button($name_button = NULL, $class = NULL)
        {
            $name_button = $name_button ? $name_button : trans('forms.button_complaint_application');
            $path_button = _r('ajax.callback.form');

            return "<button type=\"button\" data-type=\"1\" data-path=\"{$path_button}\" class=\"use-ajax uk-button uk-button-danger uk-border-rounded {$class}\">{$name_button}</button>";
        }
    }

    if (!function_exists('location_dropdown')) {
        function location_dropdown()
        {
            if (USE_SEVERAL_LOCATION) {
                $contacts = wrap()->get('contacts');
                $current_location = [
                    'id'   => $contacts['current']['id'],
                    'city' => $contacts['current']['city'],
                ];
                $all_location = $contacts['all'];

                return view('oleus.base.location_block', compact('current_location', 'all_location'))
                    ->render();
            }

            return NULL;
        }
    }

    if (!function_exists('language_menu')) {
        function language_menu($options = [])
        {
            $_options = array_merge([
                'theme' => NULL
            ], $options);
            if (USE_MULTI_LANGUAGE) {
                $current_locale = wrap()->get('locale');
                $all_languages = config('os_languages.languages');
                $_theme = is_string($_options['theme']) && view()->exists($_options['theme']) ? $_options['theme'] : 'oleus.base.language_block';

                return view($_theme, compact('current_locale', 'all_languages'))
                    ->render();
            }

            return NULL;
        }
    }

    if (!function_exists('content_render')) {
        function content_render($this_model, $object = 'body')
        {
            $_content = NULL;
            if (is_object($this_model)) {
                $_content = $this_model->{$object};
                preg_match_all('|@_short\((.*?)\)|xs', $_content, $_shorts);
                $_variables = NULL;
                if (count($_shorts[0])) {
                    $_models = config('os_short_code');
                    foreach ($_shorts[0] as $_index_short => $_data_short) {
                        $_value = explode(',', $_shorts[1][$_index_short]);
                        $_variable = [
                            'code'    => $_data_short,
                            'replace' => NULL
                        ];
                        if (isset($_value[0]) && $_value[0]) {
                            if (isset($_models[$_value[0]])) {
                                $_obj = $_models[$_value[0]];
                                $_item_model_name = $_obj['model'];
                                $_model = new $_item_model_name();
                                $entity_id = $_value[1] ? trim($_value[1]) : $this_model->id;
                                if (isset($_obj['lists'])) {
                                    $_data = $_item_model_name::where($_obj['ceil'], $entity_id)
                                        ->get();
                                } else {
                                    $_data = $_item_model_name::where($_obj['ceil'], $entity_id)
                                        ->first();
                                }
                                $_variable['replace'] = $_data ? $_model->_short_code($_data) : NULL;
                            }
                            switch ($_value[0]) {
                                case 'medias':
                                    $_files = $this_model->_medias();
                                    $_variable['replace'] = $this_model->_short_code($_files, 'medias');
                                    break;
                                case 'files':
                                    $_files = $this_model->_medias('files');
                                    $_variable['replace'] = $this_model->_short_code($_files, 'files');
                                    break;
                            }
                        }
                        $_variables[] = $_variable;
                    }
                    foreach ($_variables as $_replace_code) {
                        $_content = str_replace($_replace_code['code'], $_replace_code['replace'], $_content);
                    }
                }
            }

            return $_content;
        }
    }


    if (!function_exists('last_nodes_render')) {
        /**
         * @param array $options
         * @return null|string
         * @throws Throwable
         */
        function last_nodes_render($options = [])
        {
            $_options = array_merge([
                'entity'    => NULL,
                'take'      => 3,
                'theme'     => NULL,
                'exclude'   => [],
                'title'     => NULL,
                'more-link' => TRUE
            ], $options);
            if ($_options['entity']) {
                $item = $item = Page::where('id', $_options['entity'])
                    ->orWhere('relation', $_options['entity'])
                    ->language()
                    ->location()
                    ->first();
                if ($item && $item->status) {
                    $_last_nodes = $item->_last_nodes($_options['take'], $_options['exclude']);
                    $items = $_last_nodes->items;
                    if ($items->isNotEmpty()) {
                        if (is_string($_options['theme']) && view()->exists($_options['theme'])) $_last_nodes->template = $_options['theme'];

                        return view($_last_nodes->template, compact('item', 'items', '_options'))
                            ->render();
                    }
                }
            }

            return NULL;
        }
    }

    /**
     * @param      $file
     * @param      $field
     * @param null $view
     * @return string
     * @throws Throwable
     */
    function preview_file_render($file, $field, $view = NULL)
    {
        $_images_mimetype = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/x-icon',
            'image/vnd.microsoft.icon',
        ];
        $_template = in_array($file->filemime, $_images_mimetype) ? 'image_preview' : 'file_preview';

        return view("oleus.base.forms.{$_template}", compact('file', 'field', 'view'))
            ->render();
    }

    if (!function_exists('image_render')) {
        /**
         * @param null  $file
         * @param null  $preset
         * @param array $options
         * @return null|string|string[]
         * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        function image_render($file = NULL, $preset = NULL, $options = [])
        {
            $_default = [
                'outside_file'   => NULL,
                'no_last_modify' => FALSE,
                'only_way'       => FALSE,
                'attributes'     => [
                    'title' => $file->title ?? NULL,
                    'alt'   => $file->alt ?? NULL,
                ]
            ];
            $_options = array_merge_recursive_distinct($_default, $options);
            $presets = collect(config('os_images'));
            $path = $preset ? public_path("images/{$preset}") : public_path('images');
            $_file_path = $preset ? "images/{$preset}" : "images";
            $_file_content = NULL;
            File::isDirectory($path) or File::makeDirectory($path, 0777, TRUE, TRUE);
            if ($_options['outside_file']) {
                $file_path = $_options['outside_file']['path'];
                $file_name = $_options['outside_file']['name'];
                if (file_exists("{$path}/{$file_name}")) {
                    $_file_path = "{$_file_path}/{$file_name}";
                } else {
                    if (!$file_video = Storage::disk('uploads')->has($file_name)) {
                        Storage::disk('uploads')->put($file_name, file_get_contents($file_path));
                    }
                    if ($preset && $presets->has($preset)) {
                        $_file_path = "{$_file_path}/{$file_name}";
                        GlideImage::load($file_name)
                            ->modify($presets->get($preset))
                            ->save($_file_path);
                    } else {
                        $_file_path = "{$_file_path}/{$file_name}";
                        GlideImage::load($file_name)
                            ->save("images/{$file_name}");
                    }
                }
                $_file_path = formalize_path($_file_path, $_options['no_last_modify']);
            } else {
                $_images_mimetype = [
                    'image/jpeg',
                    'image/png',
                    'image/gif'
                ];
                $file_name = is_null($file) ? 'no-image.png' : $file->filename;
                if ((isset($file->filemime) && in_array($file->filemime, $_images_mimetype)) || is_null($file)) {
                    if (file_exists("{$path}/{$file_name}")) {
                        $_file_path = "{$_file_path}/{$file_name}";
                    } elseif ($preset && $presets->has($preset)) {
                        $_preset = $presets->get($preset);
                        $_quality = isset($_preset['quality']) && is_numeric($_preset['quality']) ? $_preset['quality'] : 90;
                        $_file = Image::make((is_null($file) ? public_path($file_name) : public_path("uploads/$file_name")));
                        $_w = isset($_preset['w']) && $_preset['w'] ? $_preset['w'] : NULL;
                        $_h = isset($_preset['h']) && $_preset['h'] ? $_preset['h'] : NULL;
                        if (isset($_preset['fit']) && $_w && $_h) {
                            $_file->fit($_w, $_h);
                        } else {
                            $_file->resize($_w, $_h, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                        $_background = isset($_preset['background']) && $_preset['background'] ? $_preset['background'] : NULL;
                        $_file->resizeCanvas($_w, $_h, 'center', FALSE, $_background);
                        if (isset($_preset['watermark']) && is_array($_preset['watermark']) && isset($_preset['watermark']['image']) && $_preset['watermark']['image']) {
                            $_watermark_position = isset($_preset['watermark']['position']) && $_preset['watermark']['position'] ? $_preset['watermark']['position'] : 'center';
                            $_watermark_position_x = $_watermark_position != 'center' ? 15 : NULL;
                            $_watermark_position_y = $_watermark_position != 'center' ? 15 : NULL;
                            $_file->insert(public_path($_preset['watermark']['image']), $_watermark_position, $_watermark_position_x, $_watermark_position_y);
                        }
                        $_file_path = "{$_file_path}/{$file_name}";
                        $_file->save(public_path($_file_path), $_quality);
                        //ImageOptimizer::optimize(public_path($_file_path));
                    } else {
                        $_file = Image::make((is_null($file) ? public_path($file_name) : public_path("uploads/$file_name")));
                        $_file_path = "{$_file_path}/{$file_name}";
                        $_file->save(public_path($_file_path), 90);
                        ImageOptimizer::optimize(public_path($_file_path));
                    }
                    $_file_path = formalize_path($_file_path, $_options['no_last_modify']);
                } elseif (isset($file->filemime) && $file->filemime == 'image/svg+xml') {
                    $_file_path = "uploads/{$file->filename}";
                    $_file_content = Storage::disk('uploads')->get($file->filename);
                } else {
                    $_file_path = formalize_path('no-image.png', $_options['no_last_modify']);
                }
            }
            if ($_options['only_way']) {
                return $_file_path;
            } elseif ($_file_content) {
                return $_file_content;
            } else {
                //                $_file_alt = !is_null($file) && $file->alt ? $file->alt : (!is_null($file) && $file->title ? $file->title : '');
                //                $_file_title = !is_null($file) && $file->title ? $file->title : '';
                //                $_options['attributes']['alt'] = $_file_alt ? $_file_alt : ((isset($_options['attributes']['alt']) && $_options['attributes']['alt']) ? $_options['attributes']['alt'] : NULL);
                //                $_options['attributes']['title'] = $_file_title ? $_file_title : ((isset($_options['attributes']['title']) && $_options['attributes']['title']) ? $_options['attributes']['title'] : NULL);
                $_attributes = render_attributes($_options['attributes']);

                return "<img src=\"{$_file_path}\" {$_attributes}>";
            }
        }
    }

