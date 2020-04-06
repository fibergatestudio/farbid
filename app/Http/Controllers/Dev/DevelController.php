<?php

    namespace App\Http\Controllers\Dev;

    use App\Http\Controllers\Controller;
    use App\Models\ShopProduct;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class DevelController extends Controller
    {
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
        }

        public function index(Request $request, $path = NULL, $page = NULL)
        {

//            $items = ShopProduct::from('shop_products as p')
//                ->with([
//                    '_alias'
//                ])
//                ->where('p.language', DEFAULT_LANGUAGE)
//                ->where('p.location', DEFAULT_LOCATION)
//                ->where('p.status', 0)
//                ->where('p.price', 0)
//                ->where('p.out_of_stock', 1)
//                ->whereNotExists(function ($_query) {
//                    $_query->select(DB::raw(1))
//                        ->from('shop_product_categories as cp')
//                        ->whereRaw('cp.product_id = p.id');
//                })
//                ->select([
//                    'p.*'
//                ])
//                ->take(500)
//                ->get();
//
//            $items->each(function($p){
//                $p->_alias->delete();
//            });

        }
    }
