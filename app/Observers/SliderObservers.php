<?php

    namespace App\Observers;

    use App\Models\Advantage;
    use App\Models\AdvantageItems;
    use App\Models\File;
    use App\Models\Slider;
    use App\Models\SliderItems;

    class SliderObservers
    {
        public function created(Slider $item)
        {
        }

        public function saved(Slider $item)
        {
            $item->forgetCache();
        }

        public function deleting(Slider $item)
        {
            $_relation_items = Slider::where('relation', $item->id)
                ->get();
            if($_relation_items->isNotEmpty()) {
                $_relation_items->each(function ($_slider) {
                    SliderItems::where('slider_id', $_slider->id)
                        ->delete();
                    $_slider->delete();
                });
            }
        }
    }