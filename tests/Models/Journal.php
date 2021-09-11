<?php

namespace Kwaadpepper\Enum\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Kwaadpepper\Enum\Tests\Enums\Days;
use Kwaadpepper\Enum\Traits\CastsEnums;

class Journal extends Model
{
    use CastsEnums;

    public $timestamps = false;

    protected $primaryKey = 'day';

    protected $keyType = 'int';

    public $incrementing = false;

    protected $fillable = [
        'day'
    ];

    protected $casts = [
        'day' => 'int'
    ];

    protected $enumCasts = [
        'day' => Days::class
    ];
}
