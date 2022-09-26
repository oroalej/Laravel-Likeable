<?php

namespace Oroalej\Likeable\Actions\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Oroalej\Likeable\Actions\LikeableCounter\IncrementLikeableCountAction;
use Oroalej\Likeable\Actions\LikerCounter\IncrementLikerCountAction;
use Oroalej\Likeable\Models\Like;

class LikedAction
{
    public function execute(Model $likeable, Model $userable): void
    {
        DB::transaction(static function () use ($likeable, $userable) {
            /** @var Like $like */
            $like = Like::withTrashed()
                ->firstOrCreate(
                    [
                        'user_type'     => get_class($userable),
                        'likeable_type' => get_class($likeable),
                        'likeable_id'   => $likeable->getKey(),
                        'user_id'       => $userable->getKey()
                    ]
                );

            if ($like->deleted_at !== null) {
                $like->restore();
            }

            ( new IncrementLikeableCountAction() )->execute($likeable);
            ( new IncrementLikerCountAction() )->execute($likeable, $userable);
        });
    }
}