<?php

namespace Model;

use \Model\Terceiro;

/**
 * 
 *
 * @author rafael
 */
class Retirada implements \JsonSerializable {
    
    private $id;
    private $terceiro;

    function __construct($id = NULL, Terceiro $terceiro = NULL) {
        $this->id = $id;
        $this->terceiro = $terceiro;
    }
    
    function getId() {
        return $this->id;
    }

    function getTerceiro() {
        return $this->terceiro;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTerceiro(Terceiro $terceiro) {
        $this->terceiro = $terceiro;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
