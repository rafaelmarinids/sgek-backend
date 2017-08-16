<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class InscricoesInformacoes implements \JsonSerializable {
    
    private $inscricoes;
    private $pagina;
    private $quantidadeRegistros;
    private $quantidadeKitsRetirados;
    private $quantidadeTotalInscricoes;

    function __construct($inscricoes = NULL, $pagina = NULL, $quantidadeRegistros = NULL, $quantidadeKitsRetirados = NULL, $quantidadeTotalInscricoes = NULL) {
        $this->inscricoes = $inscricoes;
        $this->pagina = $pagina;
        $this->quantidadeRegistros = $quantidadeRegistros;
        $this->quantidadeKitsRetirados = $quantidadeKitsRetirados;
        $this->quantidadeTotalInscricoes = $quantidadeTotalInscricoes;
    }
    
    function getInscricoes() {
        return $this->inscricoes;
    }

    function getPagina() {
        return $this->pagina;
    }

    function getQuantidadeRegistros() {
        return $this->quantidadeRegistros;
    }

    function getQuantidadeKitsRetirados() {
        return $this->quantidadeKitsRetirados;
    }

    function getQuantidadeTotalInscricoes() {
        return $this->quantidadeTotalInscricoes;
    }

    function setInscricoes($inscricoes) {
        $this->inscricoes = $inscricoes;
    }

    function setPagina($pagina) {
        $this->pagina = $pagina;
    }

    function setQuantidadeRegistros($quantidadeRegistros) {
        $this->quantidadeRegistros = $quantidadeRegistros;
    }

    function setQuantidadeKitsRetirados($quantidadeKitsRetirados) {
        $this->quantidadeKitsRetirados = $quantidadeKitsRetirados;
    }

    function setQuantidadeTotalInscricoes($quantidadeTotalInscricoes) {
        $this->quantidadeTotalInscricoes = $quantidadeTotalInscricoes;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
