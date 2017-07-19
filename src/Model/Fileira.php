<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Fileira implements \JsonSerializable {
    
    private $id;
    private $valor;
    private $indice;

    function __construct($id = NULL, $valor = NULL, $indice = NULL) {
        $this->id = $id;
        $this->valor = $valor;
        $this->indice = $indice;
    }
    
    function getId() {
        return $this->id;
    }

    function getValor() {
        return $this->valor;
    }

    function getIndice() {
        return $this->indice;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setIndice($indice) {
        $this->indice = $indice;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
