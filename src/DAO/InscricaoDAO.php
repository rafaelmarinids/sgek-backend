<?php

namespace DAO;

use \Model\Inscricao;
use \Model\Coluna;
use \Model\Fileira;
use \Model\ColunaFileira;

/**
 * 
 *
 * @author rafael
 */
class InscricaoDAO {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * 
     * @return 
     */
    public function listar($idEvento = NULL, $filtros = NULL) {
        // Tabela pivô.      
        /*$statement = $this->pdo->prepare('SET @@group_concat_max_len = 5000;
                SET @sql = NULL;
                SELECT
                GROUP_CONCAT(DISTINCT
                    CONCAT(
                    \'MAX(IF(coluna = "\',
                    coluna,
                    \'", valor, NULL)) AS "\',
                    coluna,
                    \'"\'
                    )
                ) INTO @sql
                FROM sgek.inscricao_view;
                SET @sql = CONCAT(\'SELECT id_evento, indice_valor, \', @sql, \' FROM sgek.inscricao_view WHERE id_evento = ? GROUP BY indice_valor\');
                PREPARE stmt FROM @sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;');*/

        /*SELECT i2.indice_valor FROM inscricao_view i2 WHERE (i2.indice_coluna = 2 AND i2.valor LIKE '%o%') OR (i2.indice_coluna = 6 AND i2.valor LIKE '%f%') AND i2.id_evento = 26 GROUP BY i2.indice_valor*/

        $sql = "SELECT * FROM inscricao_view i WHERE i.id_evento = $idEvento";

        if (is_array($filtros) && !empty($filtros)) {
            $in = " AND i.indice_valor IN (SELECT i2.indice_valor FROM inscricao_view i2 WHERE (";

            $numeroDeItems = count($filtros);
            
            $flag = 0;

            foreach ($filtros as $key => $value) {
                if (++$flag === $numeroDeItems) {
                    $in .= "(i2.indice_coluna = $key AND i2.valor LIKE '%$value%')";
                } else {
                    $in .= "(i2.indice_coluna = $key AND i2.valor LIKE '%$value%') OR ";
                }
            }

            $in .= ") AND i2.id_evento = $idEvento GROUP BY i2.indice_valor HAVING COUNT(i2.indice_valor) = $numeroDeItems)";

            $sql .= $in;
        }
        
        $inscricoes = array();

        $indiceAtual = NULL;
        
        foreach ($this->pdo->query($sql) as $resultado) {
            if ($indiceAtual != $resultado["indice_valor"]) {
                $indiceAtual = $resultado["indice_valor"];

                $inscricao = new Inscricao();
                $inscricao->setId($resultado["indice_valor"]);

                $inscricoes[] = $inscricao;
            }
                        
            $coluna = new Coluna();
            $coluna->setId($resultado["id_coluna"]);
            $coluna->setValor($resultado["coluna"]);
            $coluna->setIndice($resultado["indice_coluna"]);

            $fileira = new Fileira();
            $fileira->setId($resultado["id_valor"]);
            $fileira->setValor($resultado["valor"]);
            $fileira->setIndice($resultado["indice_valor"]);

            $colunaFileira = new ColunaFileira($coluna, $fileira);

            if ($resultado["usarnabusca"] == 1) {
                $arrayColunasFileirasBusca = $inscricao->getColunasFileirasBusca();

                $arrayColunasFileirasBusca[] = $colunaFileira;

                $inscricao->setColunasFileirasBusca($arrayColunasFileirasBusca);
            } else if ($resultado["usarnaconfirmacao"] == 1) {
                $arrayColunasFileirasConfirmacao = $inscricao->getColunasFileirasConfirmacao();

                $arrayColunasFileirasConfirmacao[] = $colunaFileira;

                $inscricao->setColunasFileirasConfirmacao($arrayColunasFileirasConfirmacao);
            } 
        }
        
        return $inscricoes;
    }

}
