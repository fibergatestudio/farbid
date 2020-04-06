<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Models\SiteMap;
    use Illuminate\Http\Request;

    class SiteMapController extends Controller
    {
        public function generate(Request $request)
        {
            return SiteMap::_renderXML();
        }
    }