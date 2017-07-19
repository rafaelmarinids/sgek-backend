<?php

namespace Business;

use \Exception\ValidacaoException;
use \Model\Importacao;
use \Model\Coluna;
use \Slim\Http\UploadedFile;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell;

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
    public function processarImportacao($excelTempName, $excelFileName, $mimeType) {
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

        $reader = IOFactory::createReader("Xlsx");
        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($excelTempName);

        $worksheet = $spreadsheet->getActiveSheet();

        /*foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); //This loops through all cells, even if a cell value is not set. By default, only cells that have a value set will be iterated.

            foreach ($cellIterator as $cell) {
                $cell->getValue();
            }
        }*/

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

        return $importacao;
    }
}
