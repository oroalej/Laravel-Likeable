<?php

namespace Oroalej\Likeable\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    public function likers(string $userableNamespace = null): Collection
    {
        if ($userableNamespace) {
            return Like::query()
                ->where('likeable_type', get_class($this))
                ->where('likeable_id', $this->getKey())
                ->where('userable_type', $userableNamespace)
                ->get()
                ->map(fn (Like $like) => $like->userable);
        }

        return Like::select('userable_type')
            ->where('likeable_type', get_class($this))
            ->where('likeable_id', $this->getKey())
            ->groupBy('userable_type')
            ->pluck('userable_type')
            ->map(function (string $userableType) {
                $key = Str::afterLast($userableType, '\\');

                $result = Like::with('userable')
                    ->where('likeable_type', get_class($this))
                    ->where('likeable_id', $this->getKey())
                    ->where('userable_type', $userableType)
                    ->get()
                    ->map(fn (Like $like) => $like->userable);

                return [$key => $result];
            });
    }
}
