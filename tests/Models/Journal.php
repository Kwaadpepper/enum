<?php

namespace Kwaadpepper\Enum\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Kwaadpepper\Enum\Tests\Enums\Days;
use Kwaadpepper\Enum\Traits\CastsEnums;

class Journal extends Model
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'day';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'day'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'day' => 'int'
    ];

    /**
     * The attributes that should be to enum.
     *
     * @var string[]
     */
    protected $enumCasts = [
        'day' => Days::class
    ];
}
