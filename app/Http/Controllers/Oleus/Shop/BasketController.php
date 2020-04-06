<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Bonus;
    use App\Checkout;
    use App\Gift;
    use App\Http\Controllers\Controller;
    use App\Mail\NewOrder;
    use App\Models\ShopProduct;
    use App\Node;
    use App\Order;
    use App\Param;
    use App\Product;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Session;
    use Validator;
    use View;

    class BasketController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function action(Request $request)
        {
            $_action = $request->get('action', 'add');
            $_product = $request->get('product');
            $_count = $request->get('count', 1);

            return response([
                $_action,
                $_product
            ], 200);

            $commands = [];
            if($_product = ShopProduct::find($_product)) {
                switch($_action) {
                    case 'up':
                        $_up_product = TRUE;
                        $_count = (int)Session::get("basket.{$_product->id}.count") + 1;
                        if(!$_product->out_of_stock && ($_product->not_limited || $_product->count >= $_count)) {
                            Session::put("basket.{$_product->id}.count", $_count);
                        } else {
                            $_up_product = FALSE;
                        }
                        break;
                    case 'down':
                        $_down_product = TRUE;
                        $_count = (int)Session::get("basket.{$_product->id}.count") - 1;
                        if($_count >= 1) {
                            Session::put("basket.{$_product->id}.count", $_count);
                        } else {
                            $_down_product = FALSE;
                        }
                        break;
                    case 'remove':
                        $_remove_product = TRUE;
                        if(Session::has("basket.{$_product->id}")) {
                            Session::forget("basket.{$_product->id}");
                            if(count(Session::get('basket'))) {

                            }else{
                                Session::forget('basket');
                            }
                        } else {
                            $_remove_product = FALSE;
                        }
                        break;
                    default:
                        if(Session::has("basket.{$_product->id}")) {
                            $_add_product = TRUE;
                            $_count = (int)Session::get("basket.{$_product->id}.count") + $_count;
                            if(!$_product->out_of_stock && ($_product->not_limited || $_product->count >= $_count)) {
                                Session::put("basket.{$_product->id}.count", $_count);
                            } else {
                                $commands[] = [
                                    ''
                                ];
                            }
                        } else {
                            if(!$_product->out_of_stock && ($_product->not_limited || $_product->count >= $_count)) {
                                Session::put("basket.{$_product->id}", [
                                    'product' => $_product,
                                    'price'   => $_product->price,
                                    'count'   => $_count
                                ]);
                            } else {
                                $_add_product = FALSE;
                            }
                        }
                        break;
                }
            }

            return response($commands, 200);
        }

        public function ordering(Request $request, $form)
        {
            $commands = [];


            return response($commands, 200);
        }
    }
