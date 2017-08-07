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
            
            return $usuario;
        }
        
        return NULL;
    }
}
