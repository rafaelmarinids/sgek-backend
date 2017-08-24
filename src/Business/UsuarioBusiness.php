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
     * @return type
     */
     public function recuperar($id) {
        if (!$id) {
            throw new ValidacaoException("id");
        }
            
        $usuarioDAO = new UsuarioDAO($this->pdo);
        
        return $usuarioDAO->recuperarPorId($id);
    }

    /**
     * 
     * @return type
     */
     public function listar() {        
        $usuarioDAO = new UsuarioDAO($this->pdo);
        
        return $usuarioDAO->listar();
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
                $sessao->setTipoUsuario($usuario->getTipo());
                $sessao->setAutenticado(TRUE);
                $sessao->setMensagem("Usuário autenticado com sucesso!");

                return $sessao;
            }
            
            throw new EmailSenhaInvalidosException("Senha inválida!");
        }
        
        throw new EmailSenhaInvalidosException("Email não encontrado!");
    }

    /**
     * Salva as informações do novo usuário.
     *
     * @return type
     */
     public function inserir($nome = NULL, $email = NULL, $senha = NULL, $tipo = NULL, $eventos = NULL) {
        if (!$nome) {
            throw new ValidacaoException("nome");
        }

        if (!$email) {
            throw new ValidacaoException("email");
        }

        if (!$email) {
            throw new ValidacaoException("senha");
        }

        if (!$tipo) {
            throw new ValidacaoException("tipo");
        }
        
        $usuarioDAO = new UsuarioDAO($this->pdo);

        $idUsuario = $usuarioDAO->inserir($nome, $email, password_hash($senha, PASSWORD_BCRYPT, ["cost" => 12]), $tipo, $eventos);

        //return $usuarioDAO->recuperarPorId($idUsuario);
        return NULL;
    }

    /**
     * Altera as informações do usuário.
     *
     * @return type
     */
     public function editar($id = NULL, $nome = NULL, $email = NULL, $senha = NULL, $tipo = NULL, $eventos = NULL) {
        if (!$id) {
            throw new ValidacaoException("idUsuario");
        }

        if (!$nome) {
            throw new ValidacaoException("nome");
        }

        if (!$email) {
            throw new ValidacaoException("email");
        }

        if (!$email) {
            throw new ValidacaoException("senha");
        }

        if (!$tipo) {
            throw new ValidacaoException("tipo");
        }
        
        $usuarioDAO = new UsuarioDAO($this->pdo);

        $alterado = $usuarioDAO->editar($id, $nome, $email, password_hash($senha, PASSWORD_BCRYPT, ["cost" => 12]), $tipo, $eventos);

        if ($alterado) {
            //return $usuarioDAO->recuperarPorId($id);
            return NULL;
        } else {
            throw new ValidacaoException("Não foi possível editar o usuário informado (#$id).");
        }
    }

    /**
     * Excluí um usuário.
     *
     * @return type
     */
     public function excluir($id) {
        if (!$id) {
            throw new ValidacaoException("id");
        }

        $usuarioDAO = new UsuarioDAO($this->pdo);

        $usuario = $usuarioDAO->recuperarPorId($id);
        
        if ($usuario) {
            if (!$usuarioDAO->excluir($id)) {
                throw new \Exception("Não foi possível excluir o usuário informado (#$id).");
            }
        } else {
            throw new ValidacaoException("Não foi possível excluir o usuário informado (#$id).");
        }
    }
}
