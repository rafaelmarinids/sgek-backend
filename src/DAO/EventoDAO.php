<?php

namespace DAO;

use \Model\Evento;

/**
 * 
 *
 * @author rafael
 */
class EventoDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * 
     * @return Evento
     */
    public function listar() {        
        $sql = 'SELECT * FROM evento e ORDER BY e.id DESC';
        
        $eventos = array();
        
        foreach ($this->pdo->query($sql) as $resultado) {
            $evento = new Evento();
            $evento->setId($resultado["id"]);
            $evento->setTitulo($resultado["titulo"]);
            $evento->setLogomarca($resultado["logomarca"]);
            $evento->setCor($resultado["cor"]);
            $evento->setConfirmacao($resultado["confirmacao"]);
            $evento->setPlanodefundo($resultado["planodefundo"]);
            $evento->setStatus($resultado["status"]);
            $evento->setDatahora($resultado["datahora"]);
            
            $eventos[] = $evento;
        }
        
        return $eventos;
    }
}
