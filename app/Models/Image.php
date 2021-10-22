<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $uuid
 * @property string $path
 * @property string $status
 */
class Image extends Model
{
    public const STATUS_WAITING = 'waiting';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $guarded = [];
}
