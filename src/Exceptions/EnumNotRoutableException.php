<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown on enum constructor
 * as it happens with laravel resolver
 * it is named like this event if it is only on
 * BaseEnum which works on any application
 */
class EnumNotRoutableException extends Exception
{
    //
}
