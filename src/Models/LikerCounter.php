<?php

namespace Oroalej\Likeable\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

/**
 * @property int    $id
 * @property int    $count
 * @property int    $user_id
 * @property string $user_type
 * @property string $likeable_type
 *
 * @mixin Builder
 */
class LikerCounter extends Model
{
    protected $table = 'liker_counter';

    protected $fillable = [ 'count', 'likeable_type', 'user_id', 'user_type' ];

    public $timestamps = false;

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param string   $likeableNamespace
     * @param string   $userableNamespace
     * @param int|null $userId
     * @return int
     * @throws Exception
     */
    public static function rebuild(
        string $likeableNamespace,
        string $userableNamespace,
        int $userId = null
    ): int {
        if (! class_exists($likeableNamespace)) {
            throw new Exception("$likeableNamespace Model doesn't exists");
        }

        $data = Like::query()
            ->selectRaw('count(*) as count, likeable_type, user_type, user_id')
            ->where('likeable_type', $likeableNamespace)
            ->where('user_type', $userableNamespace)
            ->when($userId, static function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->groupBy('user_id')
            ->get()
            ->toArray();

        $rebuiltCount = count($data);

        self::query()->insert($data);

        Log::info("LikerCounter Rebuild: Likeable=$likeableNamespace, Rebuilt Count=$rebuiltCount, UserModel=$userableNamespace" . $userId ? ", UserId=$userId.": ".");

        return count($data);
    }
}
