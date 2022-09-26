<?php

namespace Oroalej\Likeable\Actions\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Oroalej\Likeable\Actions\LikeableCounter\IncrementLikeableCountAction;
use Oroalej\Likeable\Actions\LikerCounter\IncrementLikerCountAction;

class LikedAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        if (! method_exists($userable, 'likes') || ! method_exists($likeable, 'likes')) {
            return;
        }

        DB::transaction(static function () use ($likeable, $userable) {
            $like = $userable->likes()
                ->withTrashed()
                ->where('likeable_type', $likeable->getMorphClass())
                ->where('likeable_id', $likeable->getKey())
                ->first();

            if ($like) {
                if (! $like->trashed()) {
                    return;
                }

                $like->restore();
            }

            $userable->likes()->create([
                'likeable_type' => $likeable->getMorphClass(),
                'likeable_id'   => $likeable->getKey(),
            ]);

            ( new IncrementLikeableCountAction() )->execute($likeable);
            ( new IncrementLikerCountAction() )->execute($likeable, $userable);
        });
    }
}
