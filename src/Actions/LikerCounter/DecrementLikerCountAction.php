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
            ->where('userable_type', $userable->getMorphClass())
            ->where('userable_id', $userable->getKey())
            ->where('likeable_type', $likeable->getMorphClass())
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
