<?php

namespace LaravelTools\WorkingDayChecker\Exceptions;

use Exception;

class WorkingDayCheckerException extends Exception
{
    // 可以自定义异常消息
    public function __construct($message)
    {
        parent::__construct($message);
    }
}