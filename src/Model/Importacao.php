<?php

namespace Model;

use \Model\Evento;

/**
 * 
 *
 * @author rafael
 */
class Importacao implements \JsonSerializable {
    
    private $id;
    private $colunas;
    private $quantidadeDeRegistros;
    private $nomeDoArquivo;
    private $evento;

    function __construct($id = NULL, $colunas = array(), $quantidadeDeRegistros = 0, $nomeDoArquivo = NULL, Evento $evento = NULL) {
        $this->colunas = $colunas;
        $this->quantidadeDeRegistros = $quantidadeDeRegistros;
        $this->nomeDoArquivo = $nomeDoArquivo;
        $this->evento = $evento;
    }

    function getId() {
        return $this->id;
    }
    
    function getColunas() {
        return $this->colunas;
    }

    function getQuantidadeDeRegistros() {
        return $this->quantidadeDeRegistros;
    }

    function getNomeDoArquivo() {
        return $this->nomeDoArquivo;
    }

    function getEvento() {
        return $this->evento;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setColunas($colunas) {
        $this->colunas = $colunas;
    }

    function setQuantidadeDeRegistros($quantidadeDeRegistros) {
        $this->quantidadeDeRegistros = $quantidadeDeRegistros;
    }

    function setNomeDoArquivo($nomeDoArquivo) {
        $this->nomeDoArquivo = $nomeDoArquivo;
    }

    function setEvento($evento) {
        $this->evento = $evento;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
