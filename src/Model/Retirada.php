<?php

namespace Model;

use \Model\Terceiro;
use \Model\Fileira;
use \Model\Usuario;

/**
 * 
 *
 * @author rafael
 */
class Retirada implements \JsonSerializable {
    
    private $id;
    private $fileira;
    private $retirado;
    private $terceiro;
    private $usuarioInsercao;
    private $dataHoraInsercao;
    private $usuarioAlteracao;
    private $dataHoraAlteracao;
    private $ocorrencia;

    function __construct($id = NULL, Fileira $fileira = NULL, $retirado = NULL, Terceiro $terceiro = NULL, 
        Usuario $usuarioInsercao = NULL, $dataHoraInsercao = NULL, Usuario $usuarioAlteracao = NULL, $dataHoraAlteracao = NULL, 
        $ocorrencia = NULL) {
        $this->id = $id;
        $this->fileira = $fileira;
        $this->retirado = $retirado;
        $this->terceiro = $terceiro;
        $this->usuarioInsercao = $usuarioInsercao;
        $this->dataHoraInsercao = $dataHoraInsercao;
        $this->usuarioAlteracao = $usuarioAlteracao;
        $this->dataHoraAlteracao = $dataHoraAlteracao;
        $this->ocorrencia = $ocorrencia;
    }
    
    function getId() {
        return $this->id;
    }

    function getFileira() {
        return $this->fileira;
    }

    function getRetirado() {
        return $this->retirado;
    }

    function getTerceiro() {
        return $this->terceiro;
    }

    function getUsuarioInsercao() {
        return $this->usuarioInsercao;
    }

    function getDataHoraInsercao() {
        return $this->dataHoraInsercao;
    }

    function getUsuarioAlteracao() {
        return $this->usuarioAlteracao;
    }

    function getDataHoraAlteracao() {
        return $this->dataHoraAlteracao;
    }

    function getOcorrencia() {
        return $this->ocorrencia;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFileira($fileira) {
        $this->fileira = $fileira;
    }

    function setRetirado($retirado) {
        $this->retirado = $retirado;
    }

    function setTerceiro($terceiro) {
        $this->terceiro = $terceiro;
    }

    function setUsuarioInsercao($usuarioInsercao) {
        $this->usuarioInsercao = $usuarioInsercao;
    }

    function setDataHoraInsercao($dataHoraInsercao) {
        $this->dataHoraInsercao = $dataHoraInsercao;
    }

    function setUsuarioAlteracao($usuarioAlteracao) {
        $this->usuarioAlteracao = $usuarioAlteracao;
    }

    function setDataHoraAlteracao($dataHoraAlteracao) {
        $this->dataHoraAlteracao = $dataHoraAlteracao;
    }

    function setOcorrencia($ocorrencia) {
        $this->ocorrencia = $ocorrencia;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
