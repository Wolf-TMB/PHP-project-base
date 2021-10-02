<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class BaseException extends Exception {
    private mixed $_options;
    private bool $isFatal;

    #[Pure] public function __construct($message, bool $isFatal = false, $code = 0, Exception $previous = null, $options = array('params')) {
        parent::__construct($message, $code, $previous);
        $this->_options = $options;
        $this->isFatal = $isFatal;
    }

    public function GetOptions() {
        return $this->_options;
    }

    public function isFatal(): bool {
        return $this->isFatal;
    }
}