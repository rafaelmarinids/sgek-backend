<?php

namespace DAO;

use \Model\Log;

/**
 * 
 *
 * @author rafael
 */
class LogDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 
     * @return Log
     */
    public function inserir($usuario = NULL, $metodo = NULL, $url = NULL, $parametros = NULL, $ip = NULL, $useragent = NULL, $observacao = NULL) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatement = $this->pdo->prepare('INSERT INTO log (usuario, metodo, url, parametros, ip, useragent, observacao, datahora) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');

            $preparedStatement->execute(array(
                $usuario,
                $metodo,
                $url,
                $parametros,
                $ip,
                $useragent,
                $observacao
            ));

            $this->pdo->commit();

            return $this->pdo->lastInsertId();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return NULL;
    }
}
