<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use App\Models\ShopCategory;

    class UrlAlias extends Model
    {
        protected $table = 'url_alias';
        protected $guarded = [];
        protected $entity;
        protected $founder;

        public function __construct($entity = NULL, $founder = NULL)
        {
            $this->entity = $entity;
            $this->founder = $founder;
        }

        public function scopeLanguage($query, $language = NULL)
        {
            $language = $language ? $language : wrap()->get('locale');

            return $query->where('language', $language);
        }

        public function scopeLocation($query, $location = NULL)
        {
            $location = $location ? $location : wrap()->get('location');

            return $query->whereNull('location')
                ->orWhere('location', $location)
                ->orderByDesc('location');
        }

        public function _items_for_menu($search_string = NULL)
        {
            $items = [];
            if($search_string) {
                $_items = self::from('url_alias as a')
                    ->leftJoin('nodes as n', 'n.id', '=', 'a.model_id')
                    ->leftJoin('pages as p', 'p.id', '=', 'a.model_id')
                    ->leftJoin('services as s', 's.id', '=', 'a.model_id')
                    ->leftJoin('shop_categories as sc', 'sc.id', '=', 'a.model_id')
                    ->leftJoin('pages as pnt', 'pnt.id', '=', 'n.entity_id')
                    ->where('a.language', DEFAULT_LANGUAGE)
                    ->where('a.location', DEFAULT_LOCATION)
                    ->where(function ($base_query) use ($search_string) {
                        $base_query->where(function ($query) use ($search_string) {
                            $query->where('a.model_type', 'App\Models\Node')
                                ->where('n.title', 'like', "%{$search_string}%")
                                ->where('n.language', DEFAULT_LANGUAGE);
                        })
                            ->orWhere(function ($query) use ($search_string) {
                                $query->where('a.model_type', 'App\Models\Page')
                                    ->where('p.title', 'like', "%{$search_string}%")
                                    ->where('p.type', '<>', 'front')
                                    ->where('p.language', DEFAULT_LANGUAGE);
                            })
                            ->orWhere(function ($query) use ($search_string) {
                                $query->where('a.model_type', 'App\Models\Service')
                                    ->where('s.title', 'like', "%{$search_string}%")
                                    ->where('s.language', DEFAULT_LANGUAGE);
                            })
                            ->orWhere(function ($query) use ($search_string) {
                                $query->where('a.model_type', 'App\Models\ShopCategory')
                                    ->where('sc.title', 'like', "%{$search_string}%")
                                    ->where('sc.language', DEFAULT_LANGUAGE);
                            });
                    })
                    ->limit(10)
                    ->get([
                        'a.id as id',
                        'a.model_id',
                        'a.model_type',
                        'n.title as node_title',
                        'n.entity_id as node_type',
                        'pnt.title as node_type_title',
                        'p.title as pages_title',
                        'p.type as pages_type',
                        's.title as services_title',
                        'sc.title as shop_category_title',
                    ]);
                if($_items->count()) {
                    $_items->each(function ($item) use (&$items) {
                        switch($item->model_type) {
                            case 'App\Models\Node':
                                $items[] = [
                                    'name' => $item->node_title,
                                    'view' => $item->node_type_title,
                                    'data' => $item->id
                                ];
                                break;
                            case 'App\Models\Page':
                                $_page = new Page();
                                $items[] = [
                                    'name' => $item->pages_title,
                                    'view' => $_page->_types($item->pages_type),
                                    'data' => $item->id
                                ];
                                break;
                            case 'App\Models\Service':
                                $items[] = [
                                    'name' => $item->services_title,
                                    'view' => trans('others.service'),
                                    'data' => $item->id
                                ];
                                break;
                            case 'App\Models\ShopCategory':
                                $_output = NULL;
                                $_category = ShopCategory::find($item->model_id);
                                if($_parents = $_category->parents) foreach($_parents as $_parent) $_output[] = $_parent->title;
                                $_output[] = $_category->title;
                                $items[] = [
                                    'name' => implode(' / ', $_output),
                                    'view' => trans('others.shop_category'),
                                    'data' => $item->id
                                ];
                                break;
                        }
                    });
                }
            }

            return $items;
        }

        public function set()
        {
            if($this->entity) {
                $_request = request()->input('url');
                $_re_render = isset($_request['re_render']) && $_request['re_render'] ? TRUE : FALSE;
                $_request_alias = isset($_request['alias']) && $_request['alias'] && $_re_render == FALSE ? $_request['alias'] : NULL;
                $_generate_alias = NULL;
                if($this->entity->alias_id) {
                    $_url_alias = self::find($this->entity->alias_id);
                    if($_request_alias && ($_request_alias != $_url_alias->alias)) {
                        $_generate_alias = generate_alias($_request_alias);
                    } elseif(!$_request_alias) {
                        $_generate_alias = generate_alias($this->entity->title, $this->founder);
                    }
                    if($_generate_alias) {
                        if($this->where('alias', $_generate_alias)
                                ->location($this->entity->location)
                                ->language($this->entity->language)
                                ->where('id', '<>', $this->entity->alias_id)
                                ->count() > 0
                        ) {
                            $index = 0;
                            while($index <= 100) {
                                $_generate_url = "{$_generate_alias}-{$index}";
                                if(self::where('alias', $_generate_url)
                                        ->where('id', '<>', $this->entity->alias_id)
                                        ->count() == 0
                                ) {
                                    $_generate_alias = $_generate_url;
                                    break;
                                }
                                $index++;
                            }
                        }

                        $_url_alias->update([
                            'alias'     => $_generate_alias,
                            're_render' => $_re_render
                        ]);
                    } else {
                        $_url_alias->update([
                            're_render' => $_re_render
                        ]);
                    }

                    return $_url_alias;
                } else {
                    $_alias = is_null($_request_alias) ? $this->entity->title : $_request_alias;
                    $_generate_alias = generate_alias($_alias, $this->founder);
                    if($this->where('alias', $_generate_alias)
                            ->location($this->entity->location)
                            ->language($this->entity->language)
                            ->count() > 0
                    ) {
                        $index = 0;
                        while($index <= 100) {
                            $_generate_url = "{$_generate_alias}-{$index}";
                            if(self::where('alias', $_generate_url)
                                    ->count() == 0
                            ) {
                                $_generate_alias = $_generate_url;
                                break;
                            }
                            $index++;
                        }
                    }
                    $_alias = $this->updateOrCreate([
                        'id' => NULL,
                    ], [
                        'model_id'   => $this->entity->id,
                        'model_type' => isset($this->entity->morph) ? $this->entity->morph : $this->entity->getMorphClass(),
                        'alias'      => $_generate_alias,
                        'language'   => $this->entity->language,
                        'location'   => $this->entity->location,
                        're_render'  => 1
                    ]);
                    $this->entity->update([
                        'alias_id' => $_alias->id
                    ]);

                    return $_alias;
                }
            }
        }

        public function model()
        {
            return $this->morphTo();
        }
    }
