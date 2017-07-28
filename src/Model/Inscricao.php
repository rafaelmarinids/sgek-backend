<?php

namespace Model;

use \Model\Retirada;

/**
 * 
 *
 * @author rafael
 */
class Inscricao implements \JsonSerializable {
    
    private $id;
    private $inscricao;
    private $colunasFileirasBusca;
    private $colunasFileirasConfirmacao;
    private $retirada;

    function __construct($id = NULL, $inscricao = NULL, $colunasFileirasBusca = array(), $colunasFileirasConfirmacao = array(), Retirada $retirada = NULL) {
        $this->id = $id;
        $this->inscricao = $inscricao;
        $this->colunasFileirasBusca = $colunasFileirasBusca;
        $this->colunasFileirasConfirmacao = $colunasFileirasConfirmacao;
        $this->retirada = $retirada;
    }
    
    function getId() {
        return $this->id;
    }

    function getInscricao() {
        return $this->inscricao;
    }

    function getColunasFileirasBusca() {
        return $this->colunasFileirasBusca;
    }

    function getColunasFileirasConfirmacao() {
        return $this->colunasFileirasConfirmacao;
    }

    function getRetirada() {
        return $this->retirada;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setInscricao($inscricao) {
        $this->inscricao = $inscricao;
    }

    function setColunasFileirasBusca($colunasFileirasBusca) {
        $this->colunasFileirasBusca = $colunasFileirasBusca;
    }

    function setColunasFileirasConfirmacao($colunasFileirasConfirmacao) {
        $this->colunasFileirasConfirmacao = $colunasFileirasConfirmacao;
    }

    function setRetirada(Retirada $retirada) {
        $this->retirada = $retirada;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
