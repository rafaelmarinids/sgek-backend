<?php

namespace Model;

/**
 * 
 *
 * @author rafael
 */
class Sessao implements \JsonSerializable {
    
    private $token;
    private $nomeUsuario;
    private $urlFotoUsuario;
    private $autenticado;
    private $mensagem;
    
    public function __construct() {      
    }

    public function getToken() {
        return $this->token;
    }

    public function getNomeUsuario() {
        return $this->nomeUsuario;
    }

    public function getUrlFotoUsuario() {
        return $this->urlFotoUsuario;
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

    public function setNomeUsuario($nomeUsuario) {
        $this->nomeUsuario = $nomeUsuario;
    }

    public function setUrlFotoUsuario($urlFotoUsuario) {
        $this->urlFotoUsuario = $urlFotoUsuario;
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
