<?php

namespace Business;

use \Exception\ValidacaoException;
use \Model\Importacao;
use \Model\Coluna;
use \Model\Fileira;
use \DAO\ImportacaoDAO;
use \Business\EventoBusiness;
use \Slim\Http\UploadedFile;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell;
use \PhpOffice\PhpSpreadsheet\Calculation\Functions;
use \PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * 
 *
 * @author rafael
 */
class ImportacaoBusiness {
    
    public static $instance;
    private $pdo;

    private function __construct() {}

    public static function getInstance($pdo = NULL) {
        if (!isset(self::$instance)) {
            self::$instance = new ImportacaoBusiness();
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
    public function processarImportacao($excelTempName, $excelFileName, $mimeType, $idEvento) {
        if (!$excelTempName) {
            throw new ValidacaoException("arquivo excel");
        }

        // Valida o tipo de arquivo.
        $mimeTypesArray = array(
            "application/vnd.ms-excel", 
            "application/msexcel", 
            "application/x-msexcel", 
            "application/x-ms-excel",
            "application/x-excel",
            "application/x-dos_ms_excel",
            "application/xls",
            "application/x-xls",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/vnd.oasis.opendocument.spreadsheet",
            "text/csv"
        ); 

        if (!in_array($mimeType, $mimeTypesArray)) {
            throw new ValidacaoException("O arquivo contendo as inscrições deve ser XLS, XLSX, ODS ou CSV.", "%s");
        }

        //$reader = IOFactory::createReader("Xlsx");
        $reader = IOFactory::createReaderForFile($excelTempName);
        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($excelTempName);

        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = Cell::columnIndexFromString($highestColumn);

        $colunas = array();

        for ($col = 0; $col <= $highestColumnIndex; ++$col) {
            $valorColuna = $worksheet->getCellByColumnAndRow($col, 1)->getValue();

            if ($valorColuna) {
                $coluna = new Coluna();
                $coluna->setIndice($col);
                $coluna->setValor($worksheet->getCellByColumnAndRow($col, 1)->getValue());

                $colunas[] = $coluna;
            }
        }

        $importacao = new Importacao();
        $importacao->setColunas($colunas);
        $importacao->setQuantidadeDeRegistros($highestRow > 0 ? $highestRow - 1 : 0);
        $importacao->setNomeDoArquivo($excelFileName);

        $eventoBusiness = EventoBusiness::getInstance($this->pdo);

        $evento = $eventoBusiness->recuperar($idEvento);

        $importacao->setId($evento->getId());
        $importacao->setEvento($evento);

        return $importacao;
    }

    /**
     * 
     * @return type
     */
    public function salvarImportacao($excelTempName, $excelFileName, $mimeType, $idEvento = NULL, $colunasObject = NULL) {
        if (!$excelTempName) {
            throw new ValidacaoException("arquivo excel");
        }

        if (!$idEvento) {
            throw new ValidacaoException("evento");
        }

        $eventoBusiness = EventoBusiness::getInstance($this->pdo);

        $evento = $eventoBusiness->recuperar($idEvento);

        if (!$evento) {
            throw new ValidacaoException("O evento informado não foi encontrado.", "%s");
        }

        if (!$colunasObject || !is_array($colunasObject) || count($colunasObject) == 0) {
            throw new ValidacaoException("É necessário selecionar ao menos uma coluna para a tela de inscrição, confirmação (caso configurado) e a identificação para inscrição.", "%s");
        }

        // Valida o tipo de arquivo.
        $mimeTypesArray = array(
            "application/vnd.ms-excel", 
            "application/msexcel", 
            "application/x-msexcel", 
            "application/x-ms-excel",
            "application/x-excel",
            "application/x-dos_ms_excel",
            "application/xls",
            "application/x-xls",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/vnd.oasis.opendocument.spreadsheet",
            "text/csv"
        ); 

        if (!in_array($mimeType, $mimeTypesArray)) {
            throw new ValidacaoException("O arquivo contendo as inscrições deve ser XLS, XLSX, ODS ou CSV.", "%s");
        }

        //$reader = IOFactory::createReader("Xlsx");
        $reader = IOFactory::createReaderForFile($excelTempName);

        // Se estiver TRUE o m[etodo isDateTime não funciona.
        //$reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($excelTempName);

        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow();

        $importacaoDAO = new ImportacaoDAO($this->pdo);

        $colunas = array();

        foreach ($colunasObject as $colunaTemp) {
            $coluna = new Coluna();
            $coluna->setEvento($evento);
            $coluna->setValor($colunaTemp->valor);
            $coluna->setIndice($colunaTemp->indice);
            $coluna->setUsarnabusca($colunaTemp->usarnabusca);
            $coluna->setUsarnaconfirmacao($colunaTemp->usarnaconfirmacao);
            $coluna->setInscricao($colunaTemp->inscricao);

            $fileiras = array();

            for ($fil = 2; $fil <= $highestRow; ++$fil) {
                $fileira = new Fileira();

                if (Date::isDateTime($worksheet->getCellByColumnAndRow($coluna->getIndice(), $fil))) {
                    $fileira->setValor(date('d/m/Y', Date::excelToTimestamp($worksheet->getCellByColumnAndRow($coluna->getIndice(), $fil)->getValue())));
                } else {
                    $fileira->setValor($worksheet->getCellByColumnAndRow($coluna->getIndice(), $fil)->getValue());
                }
                
                $fileira->setIndice($fil);

                $fileiras[] = $fileira;
            }

            $coluna->setFileiras($fileiras);
            
            $colunas[] = $coluna;
        }

        $importacaoDAO->inserirColunas($colunas);

        $eventoBusiness->salvar($evento->getId(),
            $evento->getTitulo(),
            "Ativo",
            $evento->getCor(),
            $evento->getConfirmacao());

        $importacao = new Importacao();
        $importacao->setId($evento->getId());
        $importacao->setColunas($colunasObject);
        $importacao->setQuantidadeDeRegistros($highestRow > 0 ? $highestRow - 1 : 0);
        $importacao->setNomeDoArquivo($excelFileName);
        $importacao->setEvento($evento);

        return $importacao;
    }
}
