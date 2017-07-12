<?php

namespace Business;

use \DAO\UsuarioDAO;
use \Exception\EmailSenhaInvalidosException;
use \Exception\ValidacaoException;
use \Model\Sessao;
use \Util\TokenHelper;

/**
 * 
 *
 * @author rafael
 */
class UsuarioBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new UsuarioBusiness();
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
     * @param type $email
     * @param type $senha
     * @return type
     * @throws ValidacaoException
     * @throws EmailSenhaInvalidosException
     */
    public function autenticar($email, $senha) {
        if (!$email) {
            throw new ValidacaoException("email");
        }
        
        if (!$senha) {
            throw new ValidacaoException("senha");
        }
        
        $usuarioDAO = new UsuarioDAO($this->pdo);
        
        $usuario = $usuarioDAO->recuperar($email);
        
        if ($usuario) {
            if (password_verify($senha, $usuario->getSenha())) {
                $sessao = new Sessao();
                $sessao->setToken(TokenHelper::gerarToken($usuario));
                $sessao->setNomeUsuario($usuario->getNome());
                $sessao->setUrlFotoUsuario("");
                $sessao->setAutenticado(TRUE);
                $sessao->setMensagem("Usuário autenticado com sucesso!");

                return $sessao;
            }
            
            throw new EmailSenhaInvalidosException("Senha inválida!");
        }
        
        throw new EmailSenhaInvalidosException("Email não encontrado!");
    }
}
