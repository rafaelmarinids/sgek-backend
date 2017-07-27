<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Terceiro implements \JsonSerializable {
    
    private $id;
    private $nome;
    private $telefone;
    private $documento;
    private $endereco;
    
    function __construct($id = NULL, $nome = NULL, $telefone = NULL, $documento = NULL, $endereco = NULL) {
        $this->id = $id;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->documento = $documento;
        $this->endereco = $endereco;
    }

    function getId() {
        return $this->id;
    }

    function getNome() {
        return $this->nome;
    }

    function getTelefone() {
        return $this->telefone;
    }

    function getDocumento() {
        return $this->documento;
    }

    function getEndereco() {
        return $this->endereco;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    function setDocumento($documento) {
        $this->documento = $documento;
    }

    function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
