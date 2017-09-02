<?php

namespace Viviniko\Presenter\Exceptions;

use RuntimeException;

class PresenterNotFoundException extends RuntimeException
{
    /**
     * Create a new presenter not found exception.
     *
     * @param string      $class
     * @param string|null $message
     *
     * @return void
     */
    public function __construct($class, $message = null)
    {
        if (!$message) {
            $message = "The presenter class '$class' was not found.";
        }

        parent::__construct($message);
    }
}
