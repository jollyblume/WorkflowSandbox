<?php

namespace App\Exception;

class PropImmutableException extends BaseException
{
    public function __construct(array $contextParameters = []) {
        $exceptionContext = new ExceptionContext(
            'exception.propertyimmutable',
            'debug message here',
            $contextParameters,
            403
        );
    }
}
