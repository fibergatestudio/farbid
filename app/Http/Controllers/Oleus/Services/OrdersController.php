<?php

    namespace App\Http\Controllers\Oleus\Services;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Profile;
    use App\Models\Service;
    use App\Models\ServiceOrder;
    use App\Models\ServicePrice;
    use App\User;
    use Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use View;

    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use Validator;

    class OrdersController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            $this->middleware([
                'permission:read_service_orders'
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.service_orders_page'),
                'seo._title'  => trans('pages.service_orders_page')
            ]);
            $items = ServiceOrder::orderBy('status')
                ->orderByDesc('urgently')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.services.orders', compact('items'));
        }

        public function show(ServiceOrder $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.service_orders_page_edit'),
                'seo._title'  => trans('pages.service_orders_page_edit')
            ]);
            $item->update([
                'status' => 1
            ]);

            return view('oleus.services.order_viewed', compact('item'));
        }

        public function destroy(Request $request, ServiceOrder $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.services')
                ->with('notice', [
                    'message' => trans('notice.service_deleted'),
                    'status'  => 'success'
                ]);
        }
    }
