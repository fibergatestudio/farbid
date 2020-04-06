<?php

    namespace App\Observers;

    use App\Models\Advantage;
    use App\Models\AdvantageItems;
    use App\Models\File;

    class AdvantageItemObservers
    {
        public function created(AdvantageItems $item)
        {
        }

        public function saved(AdvantageItems $item)
        {
            if(request()->has('advantage_item.icon_fid') && ($_icon = request()->input('advantage_item.icon_fid'))) {
                $_icon = array_shift($_icon);
                File::where('id', $_icon['id'])
                    ->update([
                        'title'       => $_icon['title'],
                        'alt'         => $_icon['alt'],
                        'description' => $_icon['description'],
                    ]);
            }
        }

        public function deleting(AdvantageItems $item)
        {
        }
    }