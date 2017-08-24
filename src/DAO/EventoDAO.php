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
    public function recuperar($id, $idUsuario = NULL) {
        if ($idUsuario) {
            $sql = "(SELECT e.* FROM evento e INNER JOIN usuarioevento ue ON e.id = ue.id_evento INNER JOIN usuario u ON u.id = ue.id_usuario WHERE e.id = ? AND ue.id_usuario = ? GROUP BY e.id) ";
            $sql .= "UNION ";
            $sql .= "(SELECT e2.* FROM evento e2 WHERE e2.id = ? AND (SELECT COUNT(u.id) FROM usuario u WHERE u.id = ? AND u.tipo = 'administrador') = 1) ";
            $sql .= "ORDER BY id DESC";

            $statement = $this->pdo->prepare($sql);
            
            $statement->execute(array(
                $id,
                $idUsuario,
                $id,
                $idUsuario
            ));
        } else {
            $sql = "SELECT * FROM evento e WHERE e.id = ?";

            $statement = $this->pdo->prepare($sql);
            
            $statement->execute(array(
                $id
            ));
        }

        $resultado = $statement->fetch();
        
        if ($resultado) {
            $evento = new Evento();
            $evento->setId((int) $resultado["id"]);
            $evento->setStatus($resultado["status"]);
            $evento->setTitulo($resultado["titulo"]);
            $evento->setLogomarca($resultado["logomarca"]);
            $evento->setCor($resultado["cor"]);
            $evento->setConfirmacao($resultado["confirmacao"] ? TRUE : FALSE);
            $evento->setPlanodefundo($resultado["planodefundo"]);
            $evento->setDatahora(date( 'd/m/Y H:i:s', strtotime($resultado["datahora"])));
            $evento->setMensageminicial($resultado["mensageminicial"]);
            $evento->setMensagemfinal($resultado["mensagemfinal"]);

            $evento->setImportacaoRealizada($this->contemImportacao($evento->getId()));
            
            return $evento;
        }
        
        return NULL;
    }
    
    /**
     * 
     * @return Evento
     */
    public function listar($status = NULL, $idUsuario = NULL) {
        $sql = "(SELECT e.* FROM evento e INNER JOIN usuarioevento ue ON e.id = ue.id_evento INNER JOIN usuario u ON u.id = ue.id_usuario WHERE ue.id_usuario = " . $idUsuario . " ";

        if ($status) {
            $sql .= "AND e.status = '" . $status . "' ";
        }

        $sql .= "GROUP BY e.id) ";
        $sql .= "UNION ";
        $sql .= "(SELECT e2.* FROM evento e2 WHERE (SELECT COUNT(u.id) FROM usuario u WHERE u.id = " . $idUsuario . " AND u.tipo = 'administrador') = 1) ";
        $sql .= "ORDER BY id DESC";    
        
        $eventos = array();
        
        foreach ($this->pdo->query($sql) as $resultado) {
            $evento = new Evento();
            $evento->setId((int) $resultado["id"]);
            $evento->setStatus($resultado["status"]);
            $evento->setTitulo($resultado["titulo"]);
            $evento->setLogomarca($resultado["logomarca"]);
            $evento->setCor($resultado["cor"]);
            $evento->setConfirmacao($resultado["confirmacao"] ? TRUE : FALSE);
            $evento->setPlanodefundo($resultado["planodefundo"]);
            $evento->setDatahora(date( 'd/m/Y H:i:s', strtotime($resultado["datahora"])));
            $evento->setMensageminicial($resultado["mensageminicial"]);
            $evento->setMensagemfinal($resultado["mensagemfinal"]);
            
            $eventos[] = $evento;
        }
        
        return $eventos;
    }

    /**
     * 
     * @return Evento
     */
    public function inserir($titulo, $status = NULL, $cor = NULL, $confirmacao = NULL, 
        $nomeArquivoLogomarca = NULL, $nomeArquivoPlanodefundo = NULL, $mensageminicial = NULL, $mensagemfinal = NULL) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatement = $this->pdo->prepare('INSERT INTO evento (titulo, logomarca, cor, confirmacao, planodefundo, status, datahora, mensageminicial, mensagemfinal) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)');

            $preparedStatement->execute(array(
                $titulo,
                $nomeArquivoLogomarca,
                $cor,
                $confirmacao,
                $nomeArquivoPlanodefundo,
                $status,
                $mensageminicial,
                $mensagemfinal
            ));

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return $this->pdo->lastInsertId();
    }

    /**
     * 
     * @return Evento
     */
    public function editar($id, $titulo, $status = NULL, $cor = NULL, $confirmacao = NULL, 
        $nomeArquivoLogomarca = NULL, $nomeArquivoPlanodefundo = NULL, $mensageminicial = NULL, $mensagemfinal = NULL) {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE evento e SET e.titulo = ?";

            if ($nomeArquivoLogomarca) {
                $sql .= ", e.logomarca = ?";
            }

            if ($nomeArquivoPlanodefundo) {
                $sql .= ", e.planodefundo = ?";
            }

            $sql .= ", e.cor = ?, e.confirmacao = ?, e.status = ?, e.mensageminicial = ?, e.mensagemfinal = ? WHERE e.id = ?";

            $preparedStatement = $this->pdo->prepare($sql);

            $parametros = array(
                $titulo
            );

            if ($nomeArquivoLogomarca) {
                $parametros[] = $nomeArquivoLogomarca;
            }

            if ($nomeArquivoPlanodefundo) {
                $parametros[] = $nomeArquivoPlanodefundo;
            }

            $parametros[] = $cor;
            $parametros[] = $confirmacao;
            $parametros[] = $status;
            $parametros[] = $mensageminicial;
            $parametros[] = $mensagemfinal;
            $parametros[] = $id;

            $editado = $preparedStatement->execute($parametros);

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return isset($editado) && $editado == 1 ? TRUE : FALSE;
    }

    /**
     * 
     * @return Evento
     */
    public function excluir($id) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatement = $this->pdo->prepare('DELETE FROM evento WHERE id = ?');

            $excluido = $preparedStatement->execute(array(
                $id
            ));

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return isset($excluido) && $excluido == 1 ? TRUE : FALSE;
    }

    /**
     * 
     * @return TRUE ou FALSE
     */
    public function contemImportacao($id) {        
        $statement = $this->pdo->prepare('SELECT * FROM evento e INNER JOIN tabelacoluna c ON e.id = c.id_evento WHERE e.id = ?');
        
        $statement->execute(array(
            $id
        ));

        $resultado = $statement->fetch();
        
        if ($resultado) {
            return TRUE;
        }
        
        return FALSE;
    }
}
