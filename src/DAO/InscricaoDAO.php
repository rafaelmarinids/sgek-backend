<?php

namespace DAO;

use \Model\Inscricao;
use \Model\Coluna;
use \Model\Fileira;
use \Model\ColunaFileira;
use \Model\Retirada;
use \Model\Terceiro;

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
    public function recuperarPorIdRetirada($idRetirada = NULL) {
        $statement = $this->pdo->prepare("SELECT c.id_evento, f.indice FROM (tabelacoluna c INNER JOIN tabelafileira f ON c.id = f.id_tabelacoluna) INNER JOIN retirada r ON r.id_tabelafileira = f.id WHERE r.id = ?");

        $statement->execute(array(
            $idRetirada
        ));

        $resultadoParametros = $statement->fetch();
        
        if ($resultadoParametros) {
            $statementInscricao = $this->pdo->prepare("SELECT * FROM inscricao_view i WHERE i.id_evento = ? AND i.indice_valor = ?");

            $statementInscricao->execute(array(
                $resultadoParametros["id_evento"],
                $resultadoParametros["indice"]
            ));

            $indiceAtual = NULL;
    
            while ($resultado = $statementInscricao->fetch()) {
                if ($indiceAtual != $resultado["indice_valor"]) {
                    $indiceAtual = $resultado["indice_valor"];

                    $inscricao = new Inscricao();
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

                if ($resultado["inscricao"] == 1) {
                    $inscricao->setId($resultado["id_valor"]);
                    $inscricao->setInscricao($resultado["valor"]);
                    $inscricao->setRetirada($this->recuperarRetirada($resultado["id_valor"]));
                }
            }

            return $inscricao;
        }

        return NULL;
    }
    
    /**
     * 
     * @return 
     */
    public function listar($idEvento = NULL, $filtros = NULL, $quantidadeRegistros = 25, $pagina = 1, $quantidadeColunas = 0) {
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

        $quantidadeRegistros = $quantidadeRegistros * $quantidadeColunas;

        $registroInicial = ($pagina - 1) * $quantidadeRegistros;

        $sql .= " LIMIT $registroInicial, $quantidadeRegistros";
        
        $inscricoes = array();

        $indiceAtual = NULL;
        
        foreach ($this->pdo->query($sql) as $resultado) {
            if ($indiceAtual != $resultado["indice_valor"]) {
                $indiceAtual = $resultado["indice_valor"];

                $inscricao = new Inscricao();

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
            }
            
            if ($resultado["usarnaconfirmacao"] == 1) {
                $arrayColunasFileirasConfirmacao = $inscricao->getColunasFileirasConfirmacao();

                $arrayColunasFileirasConfirmacao[] = $colunaFileira;

                $inscricao->setColunasFileirasConfirmacao($arrayColunasFileirasConfirmacao);
            }

            if ($resultado["inscricao"] == 1) {
                $inscricao->setId($resultado["id_valor"]);
                $inscricao->setInscricao($resultado["valor"]);
                $inscricao->setRetirada($this->recuperarRetirada($resultado["id_valor"]));
            }
        }
        
        return $inscricoes;
    }

    /**
     * 
     * @return 
     */
    public function recuperarRetirada($idTabelafileira = NULL) {
        $statement = $this->pdo->prepare('SELECT * FROM retirada r WHERE r.id_tabelafileira = ?');
        
        $statement->execute(array(
            $idTabelafileira
        ));

        $resultado = $statement->fetch();
        
        if ($resultado) {
            $retirada = new Retirada();
            $retirada->setId((int) $resultado["id"]);
            $retirada->setRetirado($resultado["retirado"] ? TRUE : FALSE);

            $retirada->setTerceiro($this->recuperarTerceiro($resultado["id_terceiro"]));

            $usuarioDAO = new UsuarioDAO($this->pdo);

            $retirada->setUsuarioInsercao($usuarioDAO->recuperarPorId($resultado["id_usuarioinsercao"]));
            $retirada->setDataHoraInsercao(date( 'd/m/Y H:i:s', strtotime($resultado["datahorainsercao"])));
            $retirada->setUsuarioAlteracao($usuarioDAO->recuperarPorId($resultado["id_usuarioatualizacao"]));
            $retirada->setDataHoraAlteracao(date( 'd/m/Y H:i:s', strtotime($resultado["datahoraatualizacao"])));
            $retirada->setOcorrencia($resultado["ocorrencia"]);
            
            return $retirada;
        }
        
        return new Retirada();
    }

    /**
     * 
     * @return 
     */
    public function recuperarTerceiro($idTerceiro = NULL) {
        $statement = $this->pdo->prepare('SELECT * FROM terceiro t WHERE t.id = ?');
        
        $statement->execute(array(
            $idTerceiro
        ));

        $resultado = $statement->fetch();
        
        if ($resultado) {
            $terceiro = new Terceiro();
            $terceiro->setId((int) $resultado["id"]);
            $terceiro->setNome($resultado["nome"]);
            $terceiro->setTelefone($resultado["telefone"]);
            $terceiro->setDocumento($resultado["documento"]);
            $terceiro->setEndereco($resultado["endereco"]);

            return $terceiro;
        }
        
        return new Terceiro();
    }

    /**
     * 
     * @return Coluna
     */
    public function salvarRetirada($idTabelaFileira = NULL, $retirada = NULL, $idUsuario = NULL) {
        $idRetirada = NULL;

        try {
            $this->pdo->beginTransaction();

            if ($retirada->terceiro && $retirada->terceiro->nome) {
                $preparedStatementTerceiro = $this->pdo->prepare('INSERT INTO terceiro (nome, documento, telefone, endereco) VALUES (?, ?, ?, ?)');

                $preparedStatementTerceiro->execute(array(
                    $retirada->terceiro->nome,
                    !empty($retirada->terceiro->documento) ? $retirada->terceiro->documento : NULL,
                    !empty($retirada->terceiro->telefone) ? $retirada->terceiro->telefone : NULL,
                    !empty($retirada->terceiro->endereco) ? $retirada->terceiro->endereco : NULL                 
                ));

                $idTerceiro = $this->pdo->lastInsertId();
            } else {
                $idTerceiro = NULL;
            }           

            $preparedStatementColuna = $this->pdo->prepare('INSERT INTO retirada (id_tabelafileira, retirado, id_terceiro, id_usuarioinsercao, datahorainsercao, id_usuarioatualizacao, datahoraatualizacao, ocorrencia) VALUES (?, ?, ?, ?, NOW(), ?, NOW(), ?)');
            
            $preparedStatementColuna->execute(array(
                $idTabelaFileira,
                $retirada->retirado,
                $idTerceiro,
                $idUsuario,
                $idUsuario,
                $retirada->ocorrencia
            ));

            $idRetirada = $this->pdo->lastInsertId();

            $this->pdo->commit();
        } catch(PDOException $e) {
            $this->pdo->rollBack();
        }

        return $idRetirada;
    }

}
