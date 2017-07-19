<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Coluna implements \JsonSerializable {
    
    private $id;
    private $valor;
    private $indice;
    private $usarnabusca;
    private $usarnaconfirmacao;
    private $inscricao;

    function __construct($id = NULL, $valor = NULL, $indice = NULL, $usarnabusca = FALSE, 
            $usarnaconfirmacao = FALSE, $inscricao = FALSE) {
        $this->id = $id;
        $this->valor = $valor;
        $this->indice = $indice;
        $this->usarnabusca = $usarnabusca;
        $this->usarnaconfirmacao = $usarnaconfirmacao;
        $this->inscricao = $inscricao;
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

    function getUsarnabusca() {
        return $this->usarnabusca;
    }

    function getUsarnaconfirmacao() {
        return $this->usarnaconfirmacao;
    }

    function getInscricao() {
        return $this->inscricao;
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

    function setUsarnabusca($usarnabusca) {
        $this->usarnabusca = $usarnabusca;
    }

    function setUsarnaconfirmacao($usarnaconfirmacao) {
        $this->usarnaconfirmacao = $usarnaconfirmacao;
    }

    function setInscricao($inscricao) {
        $this->inscricao = $inscricao;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
