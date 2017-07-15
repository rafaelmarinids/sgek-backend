<?php

namespace Exception;

/**
 * 
 *
 * @author rafael
 */
class ValidacaoException extends \Exception {
    
    private $mensagem = "O campo informado é inválido!";
    private $mensagemComParametro = "O campo %s é inválido!";

    public function __construct($field = NULL, $message = NULL, $code = 0, Exception $previous = null) {
        if (!$message && $field) {
            $message = sprintf($this->mensagemComParametro, $field);
        } else if ($message && $field) {
            $message = sprintf($message, $field);
        } else {
            $message = $this->message;
        }
    
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
}