<?php

namespace DAO;

use \Model\Usuario;

/**
 * 
 *
 * @author rafael
 */
class UsuarioDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 
     * @return Usuario
     */
    public function recuperarPorId($idUsuario) {
        $statement = $this->pdo->prepare('SELECT * FROM usuario u WHERE u.id = :idUsuario');
        
        $statement->execute(array(
            ':idUsuario' => $idUsuario
        ));

        $resultado = $statement->fetch();
        
        if ($resultado) {
            $usuario = new Usuario();
            $usuario->setId($resultado['id']);
            $usuario->setNome($resultado['nome']);
            $usuario->setEmail($resultado['email']);
            $usuario->setTipo($resultado['tipo']);
            $usuario->setDataHora(date( 'd/m/Y H:i:s', strtotime($resultado["dataHora"])));
            
            return $usuario;
        }
        
        return NULL;
    }
    
    /**
     * 
     * @param type $email
     * @return Usuario
     */
    public function recuperar($email) {
        $statement = $this->pdo->prepare('SELECT * FROM usuario u WHERE u.email = :email');
        
        $statement->execute(array(
            ':email' => $email
        ));

        $resultado = $statement->fetch();
        
        if ($resultado) {
            $usuario = new Usuario();
            $usuario->setId($resultado['id']);
            $usuario->setNome($resultado['nome']);
            $usuario->setEmail($resultado['email']);
            $usuario->setSenha($resultado['senha']);
            $usuario->setTipo($resultado['tipo']);
            $usuario->setDataHora(date( 'd/m/Y H:i:s', strtotime($resultado["dataHora"])));
            
            return $usuario;
        }
        
        return NULL;
    }

    /**
     * 
     * @return 
     */
    public function listar() {
        $sql = 'SELECT * FROM usuario u ORDER BY u.tipo, u.nome ASC';

        $usuarios = array();
        
        foreach ($this->pdo->query($sql) as $resultado) {
            $usuario = new Usuario();
            $usuario->setId($resultado['id']);
            $usuario->setNome($resultado['nome']);
            $usuario->setEmail($resultado['email']);
            $usuario->setTipo($resultado['tipo']);
            $usuario->setDataHora(date('d/m/Y H:i:s', strtotime($resultado["dataHora"])));
            
            $usuarios[] = $usuario;
        }
        
        return $usuarios;
    }

    /**
     * 
     * @return Evento
     */
     public function inserir($nome = NULL, $email = NULL, $senha = NULL, $tipo = NULL) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatement = $this->pdo->prepare('INSERT INTO usuario (nome, email, senha, tipo, datahora) VALUES (?, ?, ?, ?, NOW())');

            $preparedStatement->execute(array(
                $nome,
                $email,
                $senha,
                $tipo
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
     public function editar($id, $nome = NULL, $email = NULL, $senha = NULL, $tipo = NULL) {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE usuario u SET u.nome = ?, u.email = ?, u.senha = ?, u.tipo = ? WHERE u.id = ?";

            $preparedStatement = $this->pdo->prepare($sql);

            $editado = $preparedStatement->execute(array(
                $nome,
                $email,
                $senha,
                $tipo,
                $id
            ));

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return isset($editado) && $editado == 1 ? TRUE : FALSE;
    }

    /**
     * 
     * @return Usuario
     */
     public function excluir($id) {
        try {
            $this->pdo->beginTransaction();

            $preparedStatement = $this->pdo->prepare('DELETE FROM usuario WHERE id = ?');

            $excluido = $preparedStatement->execute(array(
                $id
            ));

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return isset($excluido) && $excluido == 1 ? TRUE : FALSE;
    }
}
