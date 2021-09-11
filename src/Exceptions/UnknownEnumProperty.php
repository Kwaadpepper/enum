<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown when accessing other propety than
 * label or value on a enumeration.
 */
class UnknownEnumProperty extends Exception
{

    protected $message;

    public function __construct(string $attribute)
    {
        $this->message = "Unknown attribute $attribute, enumeration only have propeties `label` and `value`.";
        parent::__construct();
    }
}
