<?php

namespace App\Exception;

class NoSuchPropertyException extends BaseException
{
    public function __construct(array $contextParameters = []) {
        $exceptionContext = new ExceptionContext(
            'exception.nosuchproperty',
            'debug message here',
            $contextParameters,
            404
        );
    }
}
