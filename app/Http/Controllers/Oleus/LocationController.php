<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Models\SiteMap;
    use Illuminate\Http\Request;
    use Session;

    class LocationController extends Controller
    {
        public function form(Request $request)
        {
            $default_location = config('app.location');
            $_choice_location = $request->input('location', $default_location);
            if($default_location == $_choice_location) {
                Session::forget('location');

                return response([
                    [
                        'command' => 'redirect',
                        'url'     => '/'
                    ]
                ], 200);
            } else {
                Session::put('location', $_choice_location);

                return response([
                    [
                        'command' => 'redirect',
                        'url'     => "/{$_choice_location}/"
                    ]
                ], 200);
            }
        }
    }