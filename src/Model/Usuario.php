<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Usuario implements \JsonSerializable {
    
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $tipo;
    private $dataHora;
    private $eventos;
    
    public function __construct($id = NULL, $nome = NULL, $email = NULL, $senha = NULL, $tipo = NULL, $dataHora = NULL, $eventos = array()) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->tipo = $tipo;
        $this->dataHora = $dataHora;
        $this->eventos = $eventos;
    }

    public function getId() {
        return $this->id;
    }

    function getNome() {
        return $this->nome;
    }

    function getEmail() {
        return $this->email;
    }

    function getSenha() {
        return $this->senha;
    }

    function getTipo() {
        return $this->tipo;
    }
    
    function getDataHora() {
        return $this->dataHora;
    }

    function getEventos() {
        return $this->eventos;
    }

    public function setId($id) {
        $this->id = $id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setSenha($senha) {
        $this->senha = $senha;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function setDataHora($dataHora) {
        $this->dataHora = $dataHora;
    }

    function setEventos($eventos) {
        $this->eventos = $eventos;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
