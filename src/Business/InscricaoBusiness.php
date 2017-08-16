<?php

namespace Business;

use \Exception\ValidacaoException;
use \DAO\InscricaoDAO;
use \DAO\ColunaDAO;
use \DAO\EventoDAO;

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
    public function listar($idEvento = NULL, $filtros = NULL, $quantidadeRegistros = 25, $pagina = 1) {
        $eventoDAO = new EventoDAO($this->pdo);

        $evento = $eventoDAO->recuperar($idEvento);

        if ($evento->getStatus() == "Inativo" || $evento->getStatus() == "Dados não importados") {
            throw new \Exception("Não é possível consultar as inscrições para o evento informado, o mesmo encontra-se inativo ou sem importação.");
        }
        
        if (!$evento->getImportacaoRealizada()) {
            throw new \Exception("Não é possível consultar as inscrições para o evento informado, a importação não foi realizada.");
        }
        
        if (!$quantidadeRegistros) {
            $quantidadeRegistros = 25;
        }

        if (!$pagina) {
            $pagina = 1;
        }

        $colunaDAO = new ColunaDAO($this->pdo);
        
        $quantidadeColunas = $colunaDAO->contar($idEvento);

        $inscricaoDAO = new InscricaoDAO($this->pdo);
        
        return $inscricaoDAO->listar($idEvento, $filtros, $quantidadeRegistros, $pagina, $quantidadeColunas);
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
    public function retirarDevolverKit($idTabelaFileira = NULL, $colunasFileirasConfirmacao = NULL, $retirada = NULL, $idUsuario = NULL) {
        if (!$idTabelaFileira) {
            throw new ValidacaoException("idTabelaFileira");
        }

        if (!$retirada || !is_object($retirada)) {
            throw new ValidacaoException("retirada");
        } else {
            if ($retirada->terceiro) {
                if (is_array($retirada->terceiro)) {
                    $retirada->terceiro = (object) $retirada->terceiro;
                }

                if (!$retirada->terceiro->nome || strlen($retirada->terceiro->nome) > 250) {
                    throw new ValidacaoException("Nome", "%s do terceiro é necessário e não pode ter mais que 250 caracateres.");
                }

                if (!$retirada->terceiro->documento || strlen($retirada->terceiro->documento) > 45) {
                    throw new ValidacaoException("Documento", "%s do terceiro é necessário e não pode ter mais que 45 caracateres.");
                }

                if (!$retirada->terceiro->telefone || strlen($retirada->terceiro->telefone) > 150) {
                    throw new ValidacaoException("Telefone", "%s do terceiro é necessário e não pode ter mais que 150 caracateres.");
                }

                if ($retirada->terceiro->endereco && strlen($retirada->terceiro->endereco) > 250) {
                    throw new ValidacaoException("Endereço", "%s do terceiro não pode ter mais que 250 caracateres.");
                }
            }
        }

        if (!$idUsuario) {
            throw new ValidacaoException("idUsuario");
        }

        $inscricaoDAO = new InscricaoDAO($this->pdo);

        $retiradaModel = $inscricaoDAO->recuperarRetirada($idTabelaFileira);

        if ($retiradaModel && $retiradaModel->getId()) {
            $idRetirada = $inscricaoDAO->alterarRetirada($idTabelaFileira, $colunasFileirasConfirmacao, $retirada, $idUsuario);
        } else {
            $idRetirada = $inscricaoDAO->salvarRetirada($idTabelaFileira, $colunasFileirasConfirmacao, $retirada, $idUsuario);
        }

        return $inscricaoDAO->recuperarPorIdRetirada($idRetirada);
    }

}
