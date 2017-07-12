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
        if (!$mensagem && $field) {
            $message = printf($this->mensagemComParametro, $field);
        } else if ($mensagem && $field) {
            $message = printf($mensagem, $field);
        } else {
            $message = $this->mensagem;
        }
    
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
}