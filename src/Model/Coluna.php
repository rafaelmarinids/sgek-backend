<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Coluna implements \JsonSerializable {
    
    private $id;
    private $evento;
    private $valor;
    private $indice;
    private $usarnabusca;
    private $usarnaconfirmacao;
    private $inscricao;
    private $fileiras;

    function __construct($id = NULL, $evento = NULL, $valor = NULL, $indice = NULL, $usarnabusca = FALSE, 
            $usarnaconfirmacao = FALSE, $inscricao = FALSE, $fileiras = array()) {
        $this->id = $id;
        $this->evento = $evento;
        $this->valor = $valor;
        $this->indice = $indice;
        $this->usarnabusca = $usarnabusca;
        $this->usarnaconfirmacao = $usarnaconfirmacao;
        $this->inscricao = $inscricao;
        $this->fileiras = $fileiras;
    }
    
    function getId() {
        return $this->id;
    }

    function getEvento() {
        return $this->evento;
    }

    function getValor() {
        return $this->valor;
    }

    function getIndice() {
        return $this->indice;
    }

    function getUsarnabusca() {
        return $this->usarnabusca;
    }

    function getUsarnaconfirmacao() {
        return $this->usarnaconfirmacao;
    }

    function getInscricao() {
        return $this->inscricao;
    }

    function getFileiras() {
        return $this->fileiras;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setEvento($evento) {
        $this->evento = $evento;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setIndice($indice) {
        $this->indice = $indice;
    }

    function setUsarnabusca($usarnabusca) {
        $this->usarnabusca = $usarnabusca;
    }

    function setUsarnaconfirmacao($usarnaconfirmacao) {
        $this->usarnaconfirmacao = $usarnaconfirmacao;
    }

    function setInscricao($inscricao) {
        $this->inscricao = $inscricao;
    }

    function setFileiras($fileiras) {
        $this->fileiras = $fileiras;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
