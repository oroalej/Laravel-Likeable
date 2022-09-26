<?php

namespace Oroalej\Likeable\Actions\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Oroalej\Likeable\Actions\LikeableCounter\DecrementLikeableCountAction;
use Oroalej\Likeable\Actions\LikerCounter\DecrementLikerCountAction;
use Oroalej\Likeable\Models\Like;

class UnlikedAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        DB::transaction(static function () use ($likeable, $userable) {
            /** @var Like $like */
            $like = Like::query()
                ->where('userable_type', get_class($userable))
                ->where('userable_id', $userable->getKey())
                ->where('likeable_type', get_class($likeable))
                ->where('likeable_id', $likeable->getKey())
                ->first();

            if ($like) {
                $like->delete();

                ( new DecrementLikeableCountAction() )->execute($likeable);
                ( new DecrementLikerCountAction() )->execute($likeable, $userable);
            }
        });
    }
}
