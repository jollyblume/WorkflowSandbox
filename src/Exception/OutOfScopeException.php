<?php

namespace App\Exception;

class OutOfScopeException extends BaseException
{
    public function __construct(array $contextParameters = []) {
        $exceptionContext = new ExceptionContext(
            'exception.outofscope',
            'debug message here',
            $contextParameters,
            403
        );
    }
}
