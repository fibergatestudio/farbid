<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Models\File;
    use Illuminate\Http\Request;
    use Storage;

    class FieldsController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function field(Request $request, $type, $action)
        {
            $commands[] = [
                'command' => 'append',
                'target'  => '#field-table-items',
                'data'    => view('oleus.base.forms.field_table_item', [
                    'name' => $request->name,
                    'cols' => $request->cols
                ])->render(),
            ];

            return response($commands, 200);
        }
    }
