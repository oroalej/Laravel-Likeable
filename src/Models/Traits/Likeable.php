<?php

namespace Oroalej\Likeable\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Oroalej\Likeable\Actions\Like\LikedAction;
use Oroalej\Likeable\Actions\Like\UnlikedAction;
use Oroalej\Likeable\Models\Like;
use Oroalej\Likeable\Models\LikeableCounter;

/**
 * @property LikeableCounter $likeableCounter
 */
trait Likeable
{
    public function likes(): morphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likeableCounter(): MorphOne
    {
        return $this->morphOne(LikeableCounter::class, 'likeable');
    }

    public function getLikeCount(): int
    {
        return $this->likeableCounter->count;
    }

    public function isLikedBy(Model $user): void
    {
        if (method_exists($user, 'likes')) {
            (new LikedAction())->execute(
                likeable: $this,
                userable: $user
            );
        }
    }

    public function isUnlikedBy(Model $user): void
    {
        if (method_exists($user, 'likes')) {
            (new UnlikedAction())->execute(
                likeable: $this,
                userable: $user
            );
        }
    }
}
