<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Callback;
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

    class CallbackController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_callback'
            ]);
        }

        public function callback()
        {
            $this->set_wrap([
                'page._title' => trans('pages.callbacks_page'),
                'seo._title'  => trans('pages.callbacks_page')
            ]);
            $items = Callback::where('type', 0)
                ->orderBy('status')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.callbacks.callback', compact('items'));
        }

        public function complaint()
        {
            $this->set_wrap([
                'page._title' => trans('pages.complaint_page'),
                'seo._title'  => trans('pages.complaint_page')
            ]);
            $items = Callback::where('type', 1)
                ->orderBy('status')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.callbacks.complaint', compact('items'));
        }

        public function show(Callback $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.callbacks_orders_page_edit'),
                'seo._title'  => trans('pages.callbacks_orders_page_edit')
            ]);
            $item->update([
                'status' => 1
            ]);

            return view('oleus.callbacks.application_viewed', compact('item'));
        }

        public function destroy(Request $request, Callback $item)
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
