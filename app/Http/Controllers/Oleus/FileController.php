<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Models\File;
    use Illuminate\Http\Request;
    use Storage;

    class FileController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function upload(Request $request)
        {
            if($request->hasFile('file')) {
                $_file = $request->file('file');
                $_file_mime_type = $_file->getClientMimeType();
                $_file_extension = $_file->getClientOriginalExtension();
                $_file_size = $_file->getClientSize();
                $_file_name = str_slug(basename($_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                Storage::disk('uploads')
                    ->put($_file_name, file_get_contents($_file->getRealPath()));
                $item = File::updateOrCreate([
                    'id' => NULL
                ], [
                    'filename' => $_file_name,
                    'filemime' => $_file_mime_type,
                    'filesize' => $_file_size,
                ]);

                return response(preview_file_render($item, $request->input('field'), $request->input('view')), 200);
            } else {
                return response(trans('notice.field_upload_not_upload'), 422);
            }
        }

        public function update(Request $request)
        {
            if($_fid = $request->input('fid')) {
                File::updateOrCreate([
                    'id' => $_fid
                ], [
                    'title'       => $request->input('title'),
                    'alt'         => $request->input('alt'),
                    'description' => $request->input('description'),
                    'sort'        => $request->input('sort'),
                ]);

                return response(trans('notice.field_upload_updated'), 200);
            } else {
                return response(trans('notice.field_upload_not_updated'), 422);
            }
        }

        public function remove(Request $request)
        {
            if($_fid = $request->input('fid')) {
                f_delete($_fid);

                return response(trans('notice.field_upload_deleted'), 200);
            } else {
                return response(trans('notice.field_upload_not_deleted'), 422);
            }
        }
    }
