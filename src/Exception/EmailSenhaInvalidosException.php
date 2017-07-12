<?php

namespace Exception;

/**
 * 
 *
 * @author rafael
 */
class EmailSenhaInvalidosException extends \Exception {

    public function __construct($message = NULL, $code = 0, Exception $previous = null) {
        if (!$message) {
            $message = "Email e/ou senha inválidos!";
        }
    
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
}