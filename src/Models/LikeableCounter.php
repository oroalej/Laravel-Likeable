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
 * @property int    $likeable_id
 * @property string $likeable_type
 *
 * @mixin Builder
 */
class LikeableCounter extends Model
{
    protected $table = 'likeable_counter';

    protected $fillable = [ 'count', 'likeable_type', 'likeable_id' ];

    public $timestamps = false;

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * @param string $modelNamespace
     * @return void
     * @throws Exception
     */
    public static function rebuild(string $modelNamespace): void
    {
        if (! class_exists($modelNamespace)) {
            throw new Exception("$modelNamespace Model doesn't exists");
        }

        $data = Like::query()
            ->selectRaw('count(*) as count, likeable_type, likeable_id')
            ->where('likeable_type', $modelNamespace)
            ->groupBy('likeable_id')
            ->get()
            ->toArray();

        $rebuiltCount = count($data);

        self::query()->insert($data);

        Log::info("LikerCounter Rebuild: Likeable=$modelNamespace, Rebuilt Count=$rebuiltCount");
    }
}
