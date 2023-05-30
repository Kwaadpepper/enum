<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown when accessing other propety than
 * label or value on a enumeration.
 */
class UnknownEnumProperty extends Exception
{
    /**
     * The error message
     *
     * @var string
     */
    protected $message;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @param string $attribute
     * @return void
     */
    public function __construct(string $attribute)
    {
        $this->message = "Unknown attribute $attribute, enumeration only have propeties `label` and `value`.";
        parent::__construct();
    }
}
