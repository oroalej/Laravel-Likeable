<?php

namespace Oroalej\Likeable\Actions\LikerCounter;

use Illuminate\Database\Eloquent\Model;
use Oroalej\Likeable\Models\LikerCounter;

class DecrementLikerCountAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        /** @var LikerCounter $item */
        $item = LikerCounter::query()
            ->where('likeable_type', get_class($likeable))
            ->where('userable_type', get_class($userable))
            ->where('userable_id', $userable->getKey())
            ->first();

        if ($item) {
            $item->count--;

            if ($item->count) {
                $item->save();
            } else {
                $item->delete();
            }
        }
    }
}
