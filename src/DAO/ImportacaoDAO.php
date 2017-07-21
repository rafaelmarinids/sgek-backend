<?php

namespace DAO;

use \Model\Coluna;
use \Model\Fileira;

/**
 * 
 *
 * @author rafael
 */
class ImportacaoDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 
     * @return Coluna
     */
    public function inserirColunas($colunas) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatementColuna = $this->pdo->prepare('INSERT INTO tabelacoluna (id_evento, valor, indice, usarnabusca, usarnaconfirmacao, inscricao) VALUES (?, ?, ?, ?, ?, ?)');
            
            $preparedStatementFileira = $this->pdo->prepare('INSERT INTO tabelafileira (id_tabelacoluna, valor, indice) VALUES (?, ?, ?)');

            foreach ($colunas as $coluna) {
                $preparedStatementColuna->execute(array(
                    $coluna->getEvento()->getId(),
                    $coluna->getValor(),
                    $coluna->getIndice(),
                    $coluna->getUsarnabusca(),
                    $coluna->getUsarnaconfirmacao(),
                    $coluna->getInscricao()
                ));

                $idColuna = $this->pdo->lastInsertId();

                foreach ($coluna->getFileiras() as $fileira) {
                    $preparedStatementFileira->execute(array(
                        $idColuna,
                        $fileira->getValor(),
                        $fileira->getIndice()                    
                    ));
                }
            }

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }
    }
}
