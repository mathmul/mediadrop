<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property string $disk
 * @property string $path
 * @property string $media_type
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Media extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'disk',
        'path',
        'media_type',
        'size',
    ];
}
