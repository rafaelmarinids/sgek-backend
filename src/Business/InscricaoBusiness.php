<?php

namespace Business;

use \Exception\ValidacaoException;
use \DAO\InscricaoDAO;
use \DAO\ColunaDAO;

/**
 * 
 *
 * @author rafael
 */
class InscricaoBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new InscricaoBusiness();
        }
        
        if ($pdo) {
           self::$instance->setPdo($pdo);
        }

        return self::$instance;
    }
    
    public function setPdo($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * 
     * @return type
     */
    public function listar($idEvento = NULL, $filtros = NULL) {
        $inscricaoDAO = new InscricaoDAO($this->pdo);
        
        return $inscricaoDAO->listar($idEvento, $filtros);
    }

    /**
     * 
     * @return type
     */
    public function listarColunas($idEvento = NULL, $usarnabusca = FALSE, $usarnaconfirmacao = FALSE) {        
        $colunaDAO = new ColunaDAO($this->pdo);
        
        return $colunaDAO->listar($idEvento, $usarnabusca, $usarnaconfirmacao);
    }

    /**
     * 
     * @return type
     */
    public function retirarKit($idTabelaFileira = NULL, $retirada = NULL, $idUsuario = NULL) {
        if (!$idTabelaFileira) {
            throw new ValidacaoException("idTabelaFileira");
        }

        if (!$retirada || !is_object($retirada)) {
            throw new ValidacaoException("retirada");
        }

        if (!$idUsuario) {
            throw new ValidacaoException("idUsuario");
        }

        $inscricaoDAO = new InscricaoDAO($this->pdo);

        $idRetirada = $inscricaoDAO->salvarRetirada($idTabelaFileira, $retirada, $idUsuario);
        
        return $inscricaoDAO->recuperarPorIdRetirada($idRetirada);
    }

}
