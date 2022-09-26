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
            'likeable_type' => get_class($likeable),
            'user_type'     => get_class($userable),
            'user_id'       => $userable->getKey()
        ]);

        $item->count++;
        $item->save();
    }
}
