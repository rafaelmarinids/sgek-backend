<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Terceiro implements \JsonSerializable {
    
    private $id;

    function __construct($id = NULL) {
        $this->id = $id;
    }
    
    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
