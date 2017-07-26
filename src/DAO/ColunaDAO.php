<?php

namespace DAO;

use \Model\Coluna;

/**
 * 
 *
 * @author rafael
 */
class ColunaDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * 
     * @return 
     */
    public function listar($idEvento = NULL, $usarnabusca = FALSE, $usarnaconfirmacao = FALSE) {        
        $sql = "SELECT * FROM tabelacoluna c WHERE c.id_evento = $idEvento ";

        if ($usarnabusca == "true") {
            $sql .= "AND c.usarnabusca = 1 ";
        }

        if ($usarnaconfirmacao == "true") {
            $sql .= "AND c.usarnaconfirmacao = 1 ";
        }

        $sql .= "ORDER BY c.indice ASC";
        
        $colunas = array();
        
        foreach ($this->pdo->query($sql) as $resultado) {
            $coluna = new Coluna();
            $coluna->setId((int) $resultado["id"]);
            $coluna->setValor($resultado["valor"]);
            $coluna->setIndice($resultado["indice"]);
            $coluna->setUsarnabusca($resultado["usarnabusca"] ? TRUE : FALSE);
            $coluna->setUsarnaconfirmacao($resultado["usarnaconfirmacao"] ? TRUE : FALSE);
            $coluna->setInscricao($resultado["inscricao"] ? TRUE : FALSE);

            $colunas[] = $coluna;
        }
        
        return $colunas;
    }

}
