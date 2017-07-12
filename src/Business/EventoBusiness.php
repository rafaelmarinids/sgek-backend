<?php

namespace Business;

use \DAO\EventoDAO;
use \Exception\ValidacaoException;

/**
 * 
 *
 * @author rafael
 */
class EventoBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new EventoBusiness();
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
    public function listar() {        
        $eventoDAO = new EventoDAO($this->pdo);
        
        return $eventoDAO->listar();
    }
}
