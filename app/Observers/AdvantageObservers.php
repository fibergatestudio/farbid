<?php

    namespace App\Observers;

    use App\Models\Advantage;
    use App\Models\File;

    class AdvantageObservers
    {
        public function created(Advantage $item)
        {
        }

        public function saved(Advantage $item)
        {
            if(request()->has('background_fid') && ($_background = request()->input('background_fid'))) {
                $_background = array_shift($_background);
                File::where('id', $_background['id'])
                    ->update([
                        'title'       => $_background['title'],
                        'alt'         => $_background['alt'],
                        'description' => $_background['description'],
                    ]);
            }
        }

        public function deleting(Advantage $item)
        {
        }
    }