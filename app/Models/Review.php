<?php

    namespace App\Models;

    use App\User;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\Paginator;

    class Review extends Model
    {
        protected $table = 'reviews',
            $primaryKey = 'id',
            $fillable = [
            'name',
            'subject',
            'review',
            'status',
            'confirm',
            'check',
            'rating',
        ];
        protected $perPage = 50;
        public $default_theme = 'oleus.base.review';

        public function scopeChecked($query)
        {
            return $query->where('check', 1);
        }

        public static function _items()
        {
            $_page = Page::where('type', 'reviews')
                ->first();
            $items = self::checked()
                ->orderByDesc('created_at');
            if($currentPage = currentPage()) {
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
            $items = $items->paginate(10);
            $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
            $_next_page = $items->currentPage() + 1;
            $_prev_page = ($_prev = $items->currentPage() - 1) && $_prev > 0 ? $_prev : 1;
            $_query_string = NULL;
            $_next_page_link = NULL;
            $_prev_page_link = NULL;
            if($queryArray = request()->query()) {
                unset($queryArray['page']);
                if(count($queryArray)) {
                    foreach($queryArray as $query => $value) {
                        if($value) {
                            $_query_string[] = "{$query}={$value}";
                        }
                    }
                    $_query_string = $_query_string ? '?' . implode('&', $_query_string) : '';
                }
            }
            if($items->currentPage() < $items->lastPage()) {
                $url = trim($_current_url, '/') . "/page-{$_next_page}";
                $_next_page_link = _u($url) . $_query_string;
            }
            if($items->currentPage() > 2) {
                $url = trim($_current_url, '/') . "/page-{$_prev_page}";
                $_prev_page_link = _u($url) . $_query_string;
            } else {
                $url = trim($_current_url, '/');
                $_prev_page_link = _u($url) . $_query_string;
            }
            wrap()->set('seo._link_prev', $_next_page_link);
            wrap()->set('seo._link_next', $_prev_page_link);
            wrap()->set('seo._page_number', $items->currentPage());
            wrap()->set('seo._canonical', $_current_url);
            if($items->currentPage() > 1) {
                $_title = wrap()->get('page._title') . '<span class="page-number uk-text-lowercase"> - ' . trans('others.page_full',
                        ['page' => $items->currentPage()]) . '</span>';
                $_description = wrap()->get('seo._description') . ' - ' . trans('others.page_full',
                        ['page' => $items->currentPage()]);
                $_suffix = '- ' . trans('others.page_full',
                        ['page' => $items->currentPage()]) . wrap()->get('seo._title_suffix');
                wrap()->set('seo._title_suffix', $_suffix);
                wrap()->set('seo._description', $_description);
                wrap()->set('page._title', $_title);
            }
            wrap()->set('breadcrumb', breadcrumb_render($_page));

            return $items;
        }
    }
