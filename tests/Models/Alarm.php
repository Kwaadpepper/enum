<?php

namespace Kwaadpepper\Enum\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Kwaadpepper\Enum\Traits\CastsEnums;

class Alarm extends Model
{
    use CastsEnums;

    public $timestamps = false;

    protected $primaryKey = 'day';

    protected $keyType = 'int';

    public $incrementing = false;

    protected $fillable = [
        'day'
    ];
}
