<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Log implements \JsonSerializable {
    
    private $id;
    private $id_usuario;
    private $metodo;
    private $url;
    private $parametros;
    private $observacao;
    private $data;
    private $datahora;

    function __construct($id, $id_usuario, $metodo, $url, $parametros, $observacao, $data, $datahora) {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->metodo = $metodo;
        $this->url = $url;
        $this->parametros = $parametros;
        $this->observacao = $observacao;
        $this->data = $data;
        $this->datahora = $datahora;
    }
    
    function getId() {
        return $this->id;
    }

    function getId_usuario() {
        return $this->id_usuario;
    }

    function getMetodo() {
        return $this->metodo;
    }

    function getUrl() {
        return $this->url;
    }

    function getParametros() {
        return $this->parametros;
    }

    function getObservacao() {
        return $this->observacao;
    }

    function getData() {
        return $this->data;
    }

    function getDatahora() {
        return $this->datahora;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    function setMetodo($metodo) {
        $this->metodo = $metodo;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setParametros($parametros) {
        $this->parametros = $parametros;
    }

    function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setDatahora($datahora) {
        $this->datahora = $datahora;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
