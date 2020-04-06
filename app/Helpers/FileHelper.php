<?php

    use App\Models\File;
    use Illuminate\Support\Facades\Storage;


    if(!function_exists('f_get')) {
        /**
         * @param $fid
         * @return \Illuminate\Support\Collection|null
         */
        function f_get($fid)
        {
            $response = NULL;
            if(is_numeric($fid)) {
                $response = Cache::rememberForever("file_{$fid}", function () use ($fid) {
                    $disk = Storage::disk('uploads');
                    $_file = File::find($fid);
                    if($_file && $disk->exists($_file->filename)) {
                        $_file->last_modified = $disk->lastModified($_file->filename);

                        return $_file;
                    }
                });
            } elseif(is_array($fid)) {
                $response = NULL;
                foreach($fid as $_file_fid) {
                    $response[] = Cache::rememberForever("file_{$_file_fid}", function () use ($_file_fid) {
                        $disk = Storage::disk('uploads');
                        $_file = File::find($_file_fid);
                        if($_file && $disk->exists($_file->filename)) {
                            $_file->last_modified = $disk->lastModified($_file->filename);

                            return $_file;
                        }
                    });
                    if($response) $response = collect($response);
                }
            }

            return $response;
        }
    }

    if(!function_exists('f_delete')) {
        /**
         * @param $fid
         */
        function f_delete($fid)
        {
            if($_file = f_get($fid)) {
                $presets = collect(config('os_images'));
                $presets->each(function ($_preset_params, $_preset_key) use ($_file) {
                    if(Storage::disk('front')->has("images/presets/{$_preset_key}/{$_file->filename}")) Storage::disk('front')->delete("images/presets/{$_preset_key}/{$_file->filename}");
                });
                if(Storage::disk('images')->has($_file->filename)) Storage::disk('images')->delete($_file->filename);
                $_file->delete();
                Cache::forget("file_{$fid}");
            }
        }
    }

    if(!function_exists('robots')) {
        /**
         * @param bool $save
         * @return array|bool|null|string
         * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        function robots($save = FALSE)
        {
            if($save) {
                $robots = request()->input('robots_txt');
                if($robots) Storage::disk('front')->put('robots.txt', $robots);

                return TRUE;
            }
            $robots = NULL;
            if(Storage::disk('front')->exists('robots.txt')) $robots = Storage::disk('front')->get('robots.txt');

            return $robots;
        }
    }
