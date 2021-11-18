<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown when your enum definition
 * contain multiple times the same label value
 *
 * This should happen if you have a duplication declaration
 */
class DuplicateDefinitionException extends Exception
{
    //
}
