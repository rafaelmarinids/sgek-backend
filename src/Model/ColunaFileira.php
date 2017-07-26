<?php

namespace Model;

use \Model\Coluna;
use \Model\Fileira;

/**
 * 
 *
 * @author rafael
 */
class ColunaFileira implements \JsonSerializable {
    
    private $coluna;
    private $fileira;

    function __construct(Coluna $coluna = NULL, Fileira $fileira = NULL) {
        $this->coluna = $coluna;
        $this->fileira = $fileira;
    }
    
    function getColuna() {
        return $this->coluna;
    }

    function getFileira() {
        return $this->fileira;
    }

    function setColuna(Coluna $coluna) {
        $this->coluna = $coluna;
    }

    function setFileira(Fileira $fileira) {
        $this->fileira = $fileira;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
