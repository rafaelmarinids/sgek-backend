<?php

namespace Business;

use \DAO\LogDAO;
use \Exception\ValidacaoException;

/**
 * 
 *
 * @author rafael
 */
class LogBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new LogBusiness();
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
     *
     * @return type
     */
    public function inserir($usuario = NULL, $metodo = NULL, $url = NULL, $parametros = NULL, $ip = NULL, $useragent = NULL, $observacao = NULL) {
        if (!$ip) {
            throw new ValidacaoException("ip");
        }

        if (!$useragent) {
            throw new ValidacaoException("useragent");
        }

        if (!$metodo) {
            throw new ValidacaoException("metodo");
        }

        if (!$url) {
            throw new ValidacaoException("url");
        }

        $logDAO = new LogDAO($this->pdo);
        
        return $logDAO->inserir($usuario, $metodo, $url, $parametros, $ip, $useragent, $observacao);
    }

}
