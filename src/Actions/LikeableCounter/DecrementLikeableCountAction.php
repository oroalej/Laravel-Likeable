<?php

namespace Oroalej\Likeable\Actions\LikeableCounter;

use Illuminate\Database\Eloquent\Model;
use Oroalej\Likeable\Models\LikeableCounter;

class DecrementLikeableCountAction
{
    public function execute(Model $likeable): void
    {
        /** @var LikeableCounter $item */
        $item = LikeableCounter::query()
            ->where('likeable_type', $likeable->getMorphClass())
            ->where('likeable_id', $likeable->getKey())
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
