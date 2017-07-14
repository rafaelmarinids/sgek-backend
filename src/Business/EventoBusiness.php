<?php

namespace Business;

use \DAO\EventoDAO;
use \Model\Evento;
use \Exception\ValidacaoException;

/**
 * 
 *
 * @author rafael
 */
class EventoBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new EventoBusiness();
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
    public function listar() {        
        $eventoDAO = new EventoDAO($this->pdo);
        
        return $eventoDAO->listar();
    }

    /**
     * 
     * @return type
     */
    public function recuperar($id) {        
        $eventoDAO = new EventoDAO($this->pdo);
        
        return $eventoDAO->recuperar($id);
    }

    /**
     * 
     * @return type
     */
    public function salvar($titulo, $status = NULL, $cor = NULL, $confirmacao = NULL, 
        UploadedFile $logomarca = NULL, UploadedFile $planodefundo = NULL) {
        if (!$titulo) {
            throw new ValidacaoException("título");
        }

        if ($logomarca && $logomarca->getError() !== UPLOAD_ERR_OK) {
            throw new ValidacaoException("logomarca", "Um erro ocorreu ao enviar o arquivo %s! (" . $logomarca->getError() . ")");
        }

        if ($planodefundo && $planodefundo->getError() !== UPLOAD_ERR_OK) {
            throw new ValidacaoException("plano de fundo", "Um erro ocorreu ao enviar o arquivo %s! (" . $planodefundo->getError() . ")");
        }

        if ($logomarca) {
            $nomeArquivoLogomarca = $this->_gerarNomeArquivo($logomarca);
        }

        if ($planodefundo) {
            $nomeArquivoPlanodefundo = $this->_gerarNomeArquivo($planodefundo);
        }
        
        $eventoDAO = new EventoDAO($this->pdo);

        try {
            $evento = $eventoDAO->salvar($titulo, $status, $cor, $confirmacao, 
                $nomeArquivoLogomarca, $nomeArquivoPlanodefundo);

            if ($logomarca) {
                $this->_moveUploadedFile($logomarca, $nomeArquivoLogomarca);
            }

            if ($planodefundo) {
                $this->_moveUploadedFile($planodefundo, $nomeArquivoPlanodefundo);
            }

            return $evento;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     *
     * @param UploadedFile $uploaded file uploaded file to move
     * @return string filename of moved file
     */
    private function _gerarNomeArquivo(UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php

        return sprintf('%s.%0.8s', $basename, $extension);
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param UploadedFile $uploaded file uploaded file to move
     * @return string filename of moved file
     */
    private function _moveUploadedFile(UploadedFile $uploadedFile, $nomeArquivo) {
        return $uploadedFile->moveTo(__DIR__ . '/../../uploads/' . $nomeArquivo);
    }
}
