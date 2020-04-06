<?php /**
 * The application's global HTTP middleware stack.
 * These middleware are run during every request to your application.
 * @var array
 */

    namespace App\Library;

    use App;
    use App\Models\ShopProductSearchHistory;
    use Carbon\Carbon;
    use Illuminate\Container\Container;
    use Illuminate\Http\Request;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    class Wrap extends Container
    {
        public $_vars;
        public $_request_method_get;

        public function __construct()
        {
            //            Cache::clear();
            //            Session::put('language', 'uk');
            //            Session::forget('language');
        }

        public function _load(Request $request)
        {
            $_csrf_token = csrf_token();
            $_auth_user = Auth::user();
            $this->instance('user', $_auth_user);
            $this->instance('token', $_csrf_token);
            $this->instance('currency', currency_load());
            //            $this->instance('basket', basket_load());
            if ($request->isMethod('GET')) {
                $this->_request_method_get = TRUE;
                request_additional_params($request);
                $device = new IdentifyDevice;
                $_device = $device->isTablet() ? 'tablet' : ($device->isMobile() ? 'mobile' : 'pc');
                $_config_app = config('app');
                $_config_seo = config('os_seo');
                $_config_i18n = config('os_languages');
                $_config_common = config('os_common');
                $_config_contacts = config('os_contacts');
                $_config_currencies = config('os_currency');
                $_config_shop = config('os_shop');
                $this->instance('is_front', FALSE);
                $this->instance('is_node', FALSE);
                $this->instance('is_page', FALSE);
                $this->instance('is_shop_product', FALSE);
                $this->instance('is_shop_category', FALSE);
                $this->instance('is_shop_filter_page', FALSE);
                $this->instance('dashboard', FALSE);
                $this->instance('device', $_device);
                $this->instance('base_url', $_config_app['url']);
                $this->instance('compress', $_config_seo['compress']);
                $this->instance('breadcrumb', NULL);
                $this->instance('multi_language', USE_MULTI_LANGUAGE);
                $this->instance('default_language', DEFAULT_LANGUAGE);
                $this->instance('languages', $_config_i18n['languages']);
                $this->instance('page._title', NULL);
                $this->instance('page._id', NULL);
                $this->instance('page._class', [
                    "user-device-{$_device}",
                    $_config_seo['page_class']
                ]);
                $this->instance('page._background', NULL);
                $this->instance('page._breadcrumbs', NULL);
                $this->instance('page._styles', []);
                $this->instance('page._scripts', []);
                $this->instance('page._js_settings', [
                    'locale'    => $_config_app['locale'],
                    'location'  => $_config_app['location'],
                    'base_url'  => $_config_app['url'],
                    'csrfToken' => $_csrf_token,
                    'apiToken'  => ($_auth_user ? $_auth_user->api_token : NULL),
                    'ajaxLoad'  => FALSE,
                    'device'    => $_device,
                    'translate' => [
                        'upload_file_mime_type' => trans('notice.field_upload_mime_type')
                    ]
                ]);
                $this->instance('seo._title', (isset($_config_seo['settings'][$_config_app['locale']]['title']) ? $_config_seo['settings'][$_config_app['locale']]['title'] : NULL));
                $this->instance('seo._title_suffix', (isset($_config_seo['settings'][$_config_app['locale']]['suffix_title']) ? $_config_seo['settings'][$_config_app['locale']]['suffix_title'] : NULL));
                $this->instance('seo._keywords', (isset($_config_seo['settings'][$_config_app['locale']]['keywords']) ? $_config_seo['settings'][$_config_app['locale']]['keywords'] : NULL));
                $this->instance('seo._description', (isset($_config_seo['settings'][$_config_app['locale']]['description']) ? $_config_seo['settings'][$_config_app['locale']]['description'] : NULL));
                // $this->instance('seo._robots', $_config_seo['robots_txt']);
                $this->instance('seo._robots', NULL);
                $this->instance('seo._favicon', ($_config_seo['favicon'] ? f_get($_config_seo['favicon']) : NULL));
                $this->instance('seo._color', $_config_seo['theme_color']);
                $this->instance('seo._copyright', (isset($_config_seo['settings'][$_config_app['locale']]['copyright']) ? $_config_seo['settings'][$_config_app['locale']]['copyright'] : NULL));
                $this->instance('seo._url', ($_config_app['url'] . getenv('REQUEST_URI')));
                $_current_url = preg_replace('/page-[0-9]+/i', '', $request->url());
                $this->instance('seo._canonical', _u(trim($_current_url, '/')));
                $this->instance('seo._current_url', (str_replace("{$_config_app['url']}/", '', url()->current())));
                $this->instance('seo._page_number', NULL);
                $this->instance('seo._link_prev', NULL);
                $this->instance('seo._link_next', NULL);
                $this->instance('seo._language', $_config_app['locale']);
                $this->instance('seo._last_modified', ($_config_seo['last_modified'] ? Carbon::now()->format('D, d M Y H:i:s \G\M\T') : NULL));
                $this->instance('site._logotype', [
                    'first' => $_config_seo['logotype']['first'] ? f_get($_config_seo['logotype']['first']) : NULL,
                    'last'  => $_config_seo['logotype']['last'] ? f_get($_config_seo['logotype']['last']) : NULL,
                    'next'  => $_config_seo['logotype']['next'] ? f_get($_config_seo['logotype']['next']) : NULL,
                    'modal' => $_config_seo['logotype']['modal'] ? f_get($_config_seo['logotype']['modal']) : NULL,
                ]);
                $this->instance('site._name', (isset($_config_seo['settings'][$_config_app['locale']]['site_name']) ? $_config_seo['settings'][$_config_app['locale']]['site_name'] : NULL));
                $this->instance('site._slogan', (isset($_config_seo['settings'][$_config_app['locale']]['site_slogan']) ? $_config_seo['settings'][$_config_app['locale']]['site_slogan'] : NULL));
                $this->instance('site._copyright', (isset($_config_seo['settings'][$_config_app['locale']]['site_copyright']) ? $_config_seo['settings'][$_config_app['locale']]['site_copyright'] : NULL));
                $this->instance('contacts', contacts_load($_config_app['locale'], $_config_app['location']));
                $this->instance('pages.search', page_load('search', $_config_app['locale']));
                $this->instance('pages.shop_basket', page_load('shop_basket', $_config_app['locale']));
                $this->instance('search_history', ShopProductSearchHistory::getHistory());
                $this->instance('variables.app', $_config_app);
                $this->instance('variables.common', $_config_common);
                $this->instance('variables.seo', $_config_seo);
                $this->instance('variables.contacts', $_config_contacts);
                $this->instance('variables.i18n', $_config_i18n);
                $this->instance('variables.currencies', $_config_currencies);
                $this->instance('variables.shop', $_config_shop);
            }
            if ($request->ajax()) {
                $_language = $request->header('locale-code', DEFAULT_LANGUAGE);
                $_location = $request->header('location-code', DEFAULT_LOCATION);
                App::setLocale($_language);
                $_config_app = config('app');
                $_config_seo = config('os_seo');
                $_config_i18n = config('os_languages');
                $_config_common = config('os_common');
                $_config_contacts = config('os_contacts');
                $_config_currencies = config('os_currency');
                $_config_shop = config('os_shop');
                $this->instance('locale', $_language);
                $this->instance('location', $_location);
                $this->instance('contacts', contacts_load($_language, $_location));
                $this->instance('seo._title', (isset($_config_seo['settings'][$_language]['title']) ? $_config_seo['settings'][$_language]['title'] : NULL));
                $this->instance('seo._title_suffix', (isset($_config_seo['settings'][$_language]['suffix_title']) ? $_config_seo['settings'][$_language]['suffix_title'] : NULL));
                $this->instance('seo._keywords', (isset($_config_seo['settings'][$_language]['keywords']) ? $_config_seo['settings'][$_language]['keywords'] : NULL));
                $this->instance('seo._description', (isset($_config_seo['settings'][$_language]['description']) ? $_config_seo['settings'][$_language]['description'] : NULL));
                $this->instance('pages.search', page_load('search', $_config_app['locale']));
                $this->instance('pages.shop_basket', page_load('shop_basket', $_config_app['locale']));
                $this->instance('variables.app', $_config_app);
                $this->instance('variables.common', $_config_common);
                $this->instance('variables.seo', $_config_seo);
                $this->instance('variables.contacts', $_config_contacts);
                $this->instance('variables.i18n', $_config_i18n);
                $this->instance('variables.currencies', $_config_currencies);
                $this->instance('variables.shop', $_config_shop);
            }
        }

        public function set($key, $value = NULL, $replace = FALSE)
        {
            $_wrap = $this->instances;
            $_value = isset($_wrap[$key]) ? $_wrap[$key] : NULL;
            if (is_string($_value) || is_null($_value) || is_bool($_value) && !$replace) {
                $_value = $value;
            } elseif (is_array($_value) || is_object($_value) && !$replace) {
                if (is_object($_value)) $_value = $_value->toArray();
                if (is_object($value)) $value = $value->toArray();
                if ($replace) {
                    $_value = $value;
                } else {
                    if (is_array($value)) {
                        $_value = array_merge($_value, $value);
                    } else {
                        $_value[] = $value;
                    }
                }
            } elseif ($replace) {
                $_value = $value;
            }
            $this->instance($key, $_value);

            return $value;
        }

        public function get($key = NULL, $default = NULL)
        {
            $_data = [];
            $_instances = $this->instances;
            foreach ($_instances as $_key => $_value) {
                $_parts = explode('.', $_key);
                $_element = &$_data;
                foreach ($_parts as $_part) $_element = &$_element[$_part];
                $_element = $_value;
            }
            if (is_null($key)) {
                return $_data;
            } else {
                return Arr::get($_data, $key, $default);
            }
        }

        public function render()
        {
            if ($this->_request_method_get) {
                $_instances = $this->instances;
                $this->render_scripts_and_styles($_instances);
                if ($_instances['breadcrumb']) $this->set('page._class', 'exists-breadcrumb');
                if ($_instances['page._class']) $this->instance('page._class', render_attributes($_instances['page._class']));
                if ($_instances['page._js_settings']) $this->instance('page._js_settings', json_encode($_instances['page._js_settings']));
            }

            return $this->get();
        }

        public function render_scripts_and_styles($_instances)
        {
            if ($_instances['page._scripts']) {
                $_render = [];
                foreach ($_instances['page._scripts'] as $_key => $script) {
                    if (is_numeric($_key)) {
                        $_file_lastModified = Storage::disk('front')->exists($script['url']) ? '?' . Storage::disk('front')->lastModified($script['url']) : NULL;
                        $_script_attributes = isset($script['attributes']) && $script['attributes'] ? ' ' . render_attributes($script['attributes']) : NULL;
                        $_script_position = isset($script['in_head']) && $script['in_head'] ? 'in_head' : 'in_footer';
                        $_script_path = $_file_lastModified ? "/{$script['url']}{$_file_lastModified}" : $script['url'];
                        if (!isset($_render[$_script_position])) $_render[$_script_position] = '';
                        $_render[$_script_position] .= "<script src=\"{$_script_path}\" type=\"text/javascript\"{$_script_attributes}></script>";
                    }
                }
                $this->instance('page._scripts', $_render);
            }
            if ($_instances['page._styles']) {
                $_render = [];
                foreach ($_instances['page._styles'] as $_key => $style) {
                    if (is_numeric($_key)) {
                        $_file_lastModified = Storage::disk('front')->exists($style['url']) ? '?' . Storage::disk('front')->lastModified($style['url']) : NULL;
                        $_style_attributes = isset($style['attributes']) && $style['attributes'] ? ' ' . render_attributes($style['attributes']) : NULL;
                        $_style_position = isset($style['in_head']) && $style['in_head'] ? 'in_head' : 'in_footer';
                        $_style_path = $_file_lastModified ? "/{$style['url']}{$_file_lastModified}" : $style['url'];
                        if (!isset($_render[$_style_position])) $_render[$_style_position] = '';
                        $_render[$_style_position] .= "<link href=\"{$_style_path}\" rel=\"stylesheet\"{$_style_attributes}>";
                    }
                }
                $this->instance('page._styles', $_render);
            }
        }

        public function can($permission)
        {
            if ($_user = $this->get('user')) return $_user->can($permission);

            return FALSE;
        }
    }
