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
            
            return $usuario;
        }
        
        return NULL;
    }
}
