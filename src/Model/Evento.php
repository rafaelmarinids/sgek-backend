<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Evento implements \JsonSerializable {
    
    private $id;
    private $titulo;
    private $logomarca;
    private $cor;
    private $confirmacao;
    private $planodefundo;
    private $status;
    private $datahora;

    // Flag de importação.
    private $importacaoRealizada;
    
    function __construct($id = NULL, $titulo = NULL, $logomarca = NULL, $cor = NULL, 
            $confirmacao = NULL, $planodefundo = NULL, $status = NULL, $datahora = NULL, 
            $importacaoRealizada = FAlSE) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->logomarca = $logomarca;
        $this->cor = $cor;
        $this->confirmacao = $confirmacao;
        $this->planodefundo = $planodefundo;
        $this->status = $status;
        $this->datahora = $datahora;
        $this->importacaoRealizada = $importacaoRealizada;
    }
    
    function getId() {
        return $this->id;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getLogomarca() {
        return $this->logomarca;
    }

    function getCor() {
        return $this->cor;
    }

    function getConfirmacao() {
        return $this->confirmacao;
    }

    function getPlanodefundo() {
        return $this->planodefundo;
    }

    function getStatus() {
        return $this->status;
    }

    function getDatahora() {
        return $this->datahora;
    }

    function getImportacaoRealizada() {
        return $this->importacaoRealizada;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setLogomarca($logomarca) {
        $this->logomarca = $logomarca;
    }

    function setCor($cor) {
        $this->cor = $cor;
    }

    function setConfirmacao($confirmacao) {
        $this->confirmacao = $confirmacao;
    }

    function setPlanodefundo($planodefundo) {
        $this->planodefundo = $planodefundo;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setDatahora($datahora) {
        $this->datahora = $datahora;
    }

    function setImportacaoRealizada($importacaoRealizada) {
        $this->importacaoRealizada = $importacaoRealizada;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
