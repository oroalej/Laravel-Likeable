<?php

namespace Oroalej\Likeable\Actions\LikeableCounter;

use Illuminate\Database\Eloquent\Model;
use Oroalej\Likeable\Models\LikeableCounter;

class IncrementLikeableCountAction
{
    public function execute(Model $likeable): void
    {
        /** @var LikeableCounter $item */
        $item = LikeableCounter::query()
            ->firstOrNew([
                'likeable_type' => get_class($likeable),
                'likeable_id'   => $likeable->getKey()
            ]);

        $item->count++;
        $item->save();
    }
}
