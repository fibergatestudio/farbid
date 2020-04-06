<?php

    namespace App\Observers;

    use App\Models\Menu;
    use App\Models\MenuItems;
    use App\Models\Profile;
    use Carbon\Carbon;

    class MenuObservers
    {
        public function created(Menu $item)
        {
        }

        public function saved(Menu $item)
        {
            $item->forgetCache();
        }

        public function deleting(Menu $item)
        {
            MenuItems::where('menu_id', $item->id)
                ->delete();
        }
    }