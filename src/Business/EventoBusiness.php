<?php

namespace Business;

use \DAO\EventoDAO;
use \Model\Evento;
use \Exception\ValidacaoException;
use \Slim\Http\UploadedFile;

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
        if (!$id) {
            throw new ValidacaoException("id");
        }
            
        $eventoDAO = new EventoDAO($this->pdo);
        
        return $eventoDAO->recuperar($id);
    }

    /**
     * Salva as informações do evento bem como move os arquivos
     * enviados para a pasta de uploads.
     *
     * @return type
     */
    public function salvar($id, $titulo, $status = NULL, $cor = NULL, $confirmacao = NULL, 
        UploadedFile $logomarca = NULL, UploadedFile $planodefundo = NULL) {
        if (!$titulo) {
            throw new ValidacaoException("título");
        }

        if ($logomarca) {
            if ($logomarca->getError() !== UPLOAD_ERR_OK) {
                throw new ValidacaoException("logomarca", "Um erro ocorreu ao enviar o arquivo %s! (" . $logomarca->getError() . ")");
            }

            $this->_validarImagemLogomarca($logomarca);
        }

        if ($planodefundo) {
            if ($planodefundo->getError() !== UPLOAD_ERR_OK) {
                throw new ValidacaoException("plano de fundo", "Um erro ocorreu ao enviar o arquivo %s! (" . $planodefundo->getError() . ")");
            }

            $this->_validarImagemPlanodefundo($planodefundo);
        }

        $nomeArquivoLogomarca = $nomeArquivoPlanodefundo = NULL;

        if ($logomarca) {
            $nomeArquivoLogomarca = $this->_gerarNomeArquivo($logomarca);
        }

        if ($planodefundo) {
            $nomeArquivoPlanodefundo = $this->_gerarNomeArquivo($planodefundo);
        }
        
        $eventoDAO = new EventoDAO($this->pdo);

        if (!$id) { // INSERIR
            $id = $eventoDAO->inserir($titulo, $status, $cor, $confirmacao, $nomeArquivoLogomarca, $nomeArquivoPlanodefundo);

            if ($logomarca) {
                $this->_moveUploadedFile($logomarca, $nomeArquivoLogomarca);
            }

            if ($planodefundo) {
                $this->_moveUploadedFile($planodefundo, $nomeArquivoPlanodefundo);
            }
        } else { // EDITAR
            $evento = $eventoDAO->recuperar($id);

            if ($evento) {
                $editado = $eventoDAO->editar($id, $titulo, $status, $cor, $confirmacao, $nomeArquivoLogomarca, $nomeArquivoPlanodefundo);

                if ($editado) {
                    if ($logomarca) {
                        $this->_removeUploadedFile($evento->getLogomarca());

                        $this->_moveUploadedFile($logomarca, $nomeArquivoLogomarca);
                    }

                    if ($planodefundo) {
                        $this->_removeUploadedFile($evento->getPlanodefundo());

                        $this->_moveUploadedFile($planodefundo, $nomeArquivoPlanodefundo);
                    }
                } else {
                    throw new ValidacaoException("Não foi possível editar o evento informado (#$id).");
                }                
            } else {
                throw new ValidacaoException("Não foi possível editar o evento informado (#$id).");
            }
        }

        $evento = $eventoDAO->recuperar($id);      

        return $evento;
    }

    /**
     * Excluí um evento.
     *
     * @return type
     */
    public function excluir($id) {
        if (!$id) {
            throw new ValidacaoException("id");
        }

        $eventoDAO = new EventoDAO($this->pdo);

        $evento = $eventoDAO->recuperar($id);
        
        if ($evento) {
            if (!$eventoDAO->excluir($id)) {
                throw new \Exception("Não foi possível excluir o evento informado (#$id).");
            } else {
                if (!empty($evento->getLogomarca())) {
                    $this->_removeUploadedFile($evento->getLogomarca());
                }
                
                if (!empty($evento->getPlanodefundo())) {
                    $this->_removeUploadedFile($evento->getPlanodefundo());
                }
            }
        } else {
            throw new ValidacaoException("Não foi possível excluir o evento informado (#$id).");
        }
    }

    /**
     * 
     *
     * @param UploadedFile $uploaded file uploaded file to move
     */
    private function _validarImagemLogomarca(UploadedFile $uploadedFile) {
        // Valida tamanho máximo 1MB.
        if ($uploadedFile->getSize() > (1024 * 1024)) {
            throw new ValidacaoException("O tamanho máximo da imagem da logomarca é de 1MB.");
        }

        // Valida o tipo de arquivo imagem.
        $mimyTypesArray = array("image/gif", "image/jpeg", "image/jpg", "image/png", "image/svg+xml"); 

        if (!in_array($uploadedFile->getClientMediaType(), $mimyTypesArray)) {
            throw new ValidacaoException("A imagem da logomarca deve ser GIF, JPG, PNG.", "%s");
        }

        // Valida dimensão máxima 150x80.
        //list($width, $height, $type, $attr) = getimagesize($uploadedFile->getFile() . "/" . $uploadedFile->getClientFilename());
        list($width, $height, $type, $attr) = getimagesize($_FILES["logomarca"]["tmp_name"]);

        if ($width && $height && ($width > 150 || $height > 80)) {
            throw new ValidacaoException("A imagem da logomarca deve estar nas dimensões 150x80 pixels.", "%s");
        }
    }

    /**
     * 
     *
     * @param UploadedFile $uploaded file uploaded file to move
     */
    private function _validarImagemPlanodefundo(UploadedFile $uploadedFile) {
        // Valida tamanho máximo 1MB.
        if ($uploadedFile->getSize() > (1024 * 1024)) {
            throw new ValidacaoException("O tamanho máximo da imagem de plano de fundo é de 1MB.");
        }

        // Valida o tipo de arquivo imagem.
        $mimyTypesArray = array("image/gif", "image/jpeg", "image/jpg", "image/png", "image/svg+xml"); 

        if (!in_array($uploadedFile->getClientMediaType(), $mimyTypesArray)) {
            throw new ValidacaoException("A imagem da logomarca deve ser GIF, JPG, PNG.", "%s");
        }

        // Valida dimensão máxima 1027x768.
        //list($width, $height, $type, $attr) = getimagesize($uploadedFile->getFilePath());
        list($width, $height, $type, $attr) = getimagesize($_FILES["planodefundo"]["tmp_name"]);

        if ($width && $height && ($width > 1024 || $height > 768)) {
            throw new ValidacaoException("A imagem da logomarca deve estar nas dimensões 1024x768 pixels.", "%s");
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

        // see http://php.net/manual/en/function.random-bytes.php
        //$basename = bin2hex(random_bytes(8)); 

        $basename = $this->_generateRandomString(8);

        return sprintf('%s.%0.8s', $basename, $extension);
    }

    /**
     * 
     *
     * @param int $length
     * @return string filename of moved file
     */
    private function _generateRandomString($length = 10) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString . "_" . date("d-m-Y_H-i-s");
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

    /*
     * Remove um arquivo informado.
     */
    private function _removeUploadedFile($nomeArquivo) {
        $arquivo = __DIR__ . '/../../uploads/' . $nomeArquivo;

        if (is_file($arquivo)) {
            return unlink($arquivo);
        }

        return FALSE;
    }
}
