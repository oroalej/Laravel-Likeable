<?php

namespace Oroalej\Likeable\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Oroalej\Likeable\Actions\Like\LikedAction;
use Oroalej\Likeable\Actions\Like\UnlikedAction;
use Oroalej\Likeable\Models\Like;
use Oroalej\Likeable\Models\LikerCounter;

trait Liker
{
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'userable');
    }

    public function likeCounter(): morphMany
    {
        return $this->morphMany(LikerCounter::class, 'userable');
    }

    public function getLikeCountByType(string $likeableNamespace): int
    {
        return $this->likeCounter()
            ->where('likeable_type', $likeableNamespace)
            ->value('count');
    }

    public function getTotalLikeCount(): int
    {
        return $this->likeCounter()
            ->sum('count');
    }

    public function like(Model $model): void
    {
        ( new LikedAction() )->execute(
            likeable: $model,
            userable: $this
        );
    }

    public function unlike(Model $model): void
    {
        ( new UnlikedAction() )->execute(
            likeable: $model,
            userable: $this
        );
    }

    public function liked(string $modelNamespace = null): Collection
    {
        if ($modelNamespace) {
            return Like::with('likeable')
                ->where('userable_type', $this->getMorphClass())
                ->where('userable_id', $this->getKey())
                ->where('likeable_type', $modelNamespace)
                ->get()
                ->map(fn ($like) => $like->likeable);
        }

        $result = Like::with('likeable')
            ->where('userable_type', $this->getMorphClass())
            ->where('userable_id', $this->getKey())
            ->get();

        return Like::select('likeable_type')
            ->where('userable_type', $this->getMorphClass())
            ->where('userable_id', $this->getKey())
            ->groupBy('likeable_type')
            ->pluck('likeable_type')
            ->map(function (string $likeableType) use ($result) {
                $key = Str::afterLast($likeableType, '\\');

                $filteredData = $result->where('likeable_type', $likeableType)
                    ->map(fn(Like $like) => $like->likeable);

                return [$key => $filteredData];
            });
    }
}
