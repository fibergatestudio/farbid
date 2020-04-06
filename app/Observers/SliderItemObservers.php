<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\SliderItems;

    class SliderItemObservers
    {
        public function created(SliderItems $item)
        {
        }

        public function saved(SliderItems $item)
        {
            if(request()->has('slider_item.background_fid') && ($_background = request()->input('slider_item.background_fid'))) {
                $_background = array_shift($_background);
                File::where('id', $_background['id'])
                    ->update([
                        'title'       => $_background['title'],
                        'alt'         => $_background['alt'],
                        'description' => $_background['description'],
                    ]);
            }
        }

        public function deleting(SliderItems $item)
        {
        }
    }