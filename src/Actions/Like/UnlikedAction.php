<?php

namespace Oroalej\Likeable\Actions\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Oroalej\Likeable\Actions\LikeableCounter\DecrementLikeableCountAction;
use Oroalej\Likeable\Actions\LikerCounter\DecrementLikerCountAction;

class UnlikedAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        if (! method_exists($userable, 'likes') || ! method_exists($likeable, 'likes')) {
            return;
        }

        DB::transaction(static function () use ($likeable, $userable) {
            $like = $userable->likes()
                ->where('likeable_type', $likeable->getMorphClass())
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
