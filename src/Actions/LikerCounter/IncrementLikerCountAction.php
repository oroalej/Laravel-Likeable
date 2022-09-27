<?php

namespace Oroalej\Likeable\Actions\LikerCounter;

use Illuminate\Database\Eloquent\Model;
use Oroalej\Likeable\Models\LikerCounter;

class IncrementLikerCountAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        /** @var LikerCounter $item */
        $item = LikerCounter::query()->firstOrNew([
            'userable_type'     => $userable->getMorphClass(),
            'userable_id'       => $userable->getKey(),
            'likeable_type' => $likeable->getMorphClass()
        ]);

        $item->count++;
        $item->save();
    }
}
