<?php

namespace Oroalej\Likeable\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
        return $this->morphMany(Like::class, 'user');
    }

    public function likeCounter(): MorphToMany
    {
        return $this->MorphToMany(LikerCounter::class, 'liker');
    }

    public function getLikeCounterByType(string $modelNamespace = null)
    {
        return $this->likeCounter()
            ->when($modelNamespace, function (Builder $builder) use ($modelNamespace) {
                $builder->where('likeable_type', $modelNamespace);
            });
    }

    public function getTotalLikeCount(): int
    {
        return $this->likeCounter()
            ->sum('count');
    }

    public function like(Model $model): void
    {
        if (method_exists($model, 'likes')) {
            ( new LikedAction() )->execute(
                likeable: $model,
                userable: $this
            );
        }
    }

    public function unlike(Model $model): void
    {
        if (method_exists($model, 'likes')) {
            ( new UnlikedAction() )->execute(
                likeable: $model,
                userable: $this
            );
        }
    }

    public function liked(string $modelNamespace = null): Collection
    {
        if ($modelNamespace) {
            return Like::with('likeable')
                ->where('userable_type', get_class($this))
                ->where('userable_id', $this->getKey())
                ->where('likeable_type', $modelNamespace)
                ->get()
                ->map(fn ($like) => $like->likeable);
        }

        return Like::select('likeable_type')
            ->where('userable_type', get_class($this))
            ->where('userable_id', $this->getKey())
            ->groupBy('likeable_type')
            ->pluck('likeable_type')
            ->map(function (string $likeableType) {
                $key = Str::afterLast($likeableType, '\\');

                $result = Like::with('likeable')
                    ->where('userable_type', get_class($this))
                    ->where('userable_id', $this->getKey())
                    ->where('likeable_type', $likeableType)
                    ->get()
                    ->map(fn (Like $like) => $like->likeable);

                return [$key => $result];
            });
    }
}
