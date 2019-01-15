<?php

namespace App\Exception;

class PropRequiredException extends BaseException
{
    public function __construct(array $contextParameters = []) {
        $exceptionContext = new ExceptionContext(
            'exception.propertyrequired',
            'debug message here',
            $contextParameters,
            500
        );
    }
}
