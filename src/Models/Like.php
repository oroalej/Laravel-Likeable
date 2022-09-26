<?php

namespace Oroalej\Likeable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $userable_id
 * @property int $likeable_id
 * @property string $userable_type
 * @property string $likeable_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property MorphTo $likeable
 * @property MorphTo $userable
 *
 * @mixin Builder
 */
class Like extends Model
{
    use SoftDeletes;

    protected $table = 'likes';

    protected $guarded = [];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function userable(): BelongsTo
    {
        return $this->morphTo('userable');
    }
}
