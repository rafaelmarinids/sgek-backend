<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Sessao implements \JsonSerializable {
    
    private $token;
    private $idUsuario;
    private $nomeUsuario;
    private $urlFotoUsuario;
    private $tipoUsuario;
    private $autenticado;
    private $mensagem;
    
    public function __construct() {      
    }

    public function getToken() {
        return $this->token;
    }

    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getNomeUsuario() {
        return $this->nomeUsuario;
    }

    public function getUrlFotoUsuario() {
        return $this->urlFotoUsuario;
    }

    public function getTipoUsuario() {
        return $this->tipoUsuario;
    }

    public function getAutenticado() {
        return $this->autenticado;
    }

    public function getMensagem() {
        return $this->mensagem;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function setNomeUsuario($nomeUsuario) {
        $this->nomeUsuario = $nomeUsuario;
    }

    public function setUrlFotoUsuario($urlFotoUsuario) {
        $this->urlFotoUsuario = $urlFotoUsuario;
    }

    public function setTipoUsuario($tipoUsuario) {
        $this->tipoUsuario = $tipoUsuario;
    }

    public function setAutenticado($autenticado) {
        $this->autenticado = $autenticado;
    }

    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
