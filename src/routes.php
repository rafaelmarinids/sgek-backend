<?php

// Routes

/*$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/

use \Business\UsuarioBusiness;
use \Business\EventoBusiness;
use \Business\ImportacaoBusiness;
use \Business\InscricaoBusiness;

/*
 * Webservices.
 */
$app->group('/rs', function () {
    /*
     * Autenticação.
     */
    $this->post('/autenticacao', function ($request, $response, $args) {
        $data = $request->getParsedBody();
        
        $usuarioBusiness = UsuarioBusiness::getInstance($this->db);
        
        try {
            $sessao = $usuarioBusiness->autenticar(filter_var($data['email'], FILTER_SANITIZE_STRING), 
                    filter_var($data['senha'], FILTER_SANITIZE_STRING));
            
            return $response->withJson($sessao);
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }        
    });
    
    /*
     * Renova a autenticação.
     */
    $this->get('/renovar-autenticacao', function ($request, $response, $args) {
        return $response;
    });
    
    /*
     * Recupera a lista de eventos.
     */
    $this->get('/eventos', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        $parametros = $request->getQueryParams();

        $status = is_array($parametros) && array_key_exists("status", $parametros) ? filter_var($parametros["status"], FILTER_SANITIZE_STRING) : NULL;

        $eventos = $eventoBusiness->listar($status);
        
        return $response->withJson($eventos);
    });

    /*
     * Recupera um evento por id.
     */
    $this->get('/eventos/{id}', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        try {
            return $response->withJson($eventoBusiness->recuperar((int) $args["id"]));
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }
    });

    /*
     * Inseri um evento.
     */
    $this->post('/eventos', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        $parametros = $request->getParsedBody();

        $arquivos = $request->getUploadedFiles();

        try {
            $evento = $eventoBusiness->salvar(NULL,
                filter_var($parametros['titulo'], FILTER_SANITIZE_STRING),
                filter_var($parametros['status'], FILTER_SANITIZE_STRING),
                filter_var($parametros['cor'], FILTER_SANITIZE_STRING),
                filter_var($parametros['confirmacao'], FILTER_SANITIZE_STRING),
                $arquivos && count($arquivos) && array_key_exists("logomarca", $arquivos) ? $arquivos["logomarca"] : NULL,
                $arquivos && count($arquivos) && array_key_exists("planodefundo", $arquivos) ? $arquivos["planodefundo"] : NULL);
            
            return $response->withJson($evento);
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }
    });

    /*
     * Edita um evento.
     */
    // O PHP não implementa um parser para requisção PUT do tipo multipart-formdata.
    //$this->put('/eventos/{id}', function ($request, $response, $args) {
    $this->post('/eventos/{id}', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        $parametros = $request->getParsedBody();

        $arquivos = $request->getUploadedFiles();

        try {
            $evento = $eventoBusiness->salvar((int) $args["id"],
                filter_var($parametros['titulo'], FILTER_SANITIZE_STRING),
                filter_var($parametros['status'], FILTER_SANITIZE_STRING),
                filter_var($parametros['cor'], FILTER_SANITIZE_STRING),
                filter_var($parametros['confirmacao'], FILTER_SANITIZE_STRING),
                $arquivos && count($arquivos) && array_key_exists("logomarca", $arquivos) ? $arquivos["logomarca"] : NULL,
                $arquivos && count($arquivos) && array_key_exists("planodefundo", $arquivos) ? $arquivos["planodefundo"] : NULL);
            
            return $response->withJson($evento);
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }
    });

    /*
     * Remove um evento por id.
     */
    $this->delete('/eventos/{id}', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        try {
            $eventoBusiness->excluir((int) $args["id"]);

            return $response->withStatus(204);
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }
    });

    /*
     * Processa as informações extraídas do arquivo excel
     * e retorna as informações de inscrição.
     *
     * Processa as informações extraídas do arquivo excel
     * e salva as informações de acordo com as colunas selecionadas.
     */
    $this->post('/importacao[/{id}]', function ($request, $response, $args) {
        $importacaoBusiness = ImportacaoBusiness::getInstance($this->db);

        $parametros = $request->getParsedBody();

        $arquivos = $request->getUploadedFiles();

        try {
            // Processa a importação.
            if (!array_key_exists("id", $args)) {
                $importacao = $importacaoBusiness->processarImportacao($_FILES["excel"]["tmp_name"], 
                    $arquivos["excel"]->getClientFilename(),
                    $arquivos["excel"]->getClientMediaType(),
                    $parametros["evento"]);
            // Salva a importação.
            } else {
                $importacao = $importacaoBusiness->salvarImportacao($_FILES["excel"]["tmp_name"], 
                    $arquivos["excel"]->getClientFilename(),
                    $arquivos["excel"]->getClientMediaType(),
                    $parametros["evento"],
                    json_decode($parametros["colunas"]));
            }

            return $response->withJson($importacao);
        } catch (\Exception $e) {
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/plain')
                    ->write($e->getMessage());
        }
    });

     /*
     * Recupera a lista de colunas de um determinado evento.
     */
    $this->get('/colunas', function ($request, $response, $args) {
        $inscricaoBusiness = InscricaoBusiness::getInstance($this->db);

        $parametros = $request->getQueryParams();

        $evento = is_array($parametros) && array_key_exists("evento", $parametros) ? filter_var($parametros["evento"], FILTER_SANITIZE_STRING) : NULL;
        $usarnabusca = is_array($parametros) && array_key_exists("usarnabusca", $parametros) ? filter_var($parametros["usarnabusca"], FILTER_SANITIZE_STRING) : NULL;
        $usarnaconfirmacao = is_array($parametros) && array_key_exists("usarnaconfirmacao", $parametros) ? filter_var($parametros["usarnaconfirmacao"], FILTER_SANITIZE_STRING) : NULL;

        $colunas = $inscricaoBusiness->listarColunas($evento, $usarnabusca, $usarnaconfirmacao);
        
        return $response->withJson($colunas);
    });

    /*
     * Recupera a lista de inscrições de um determinado evento.
     */
    $this->get('/inscricoes', function ($request, $response, $args) {
        $inscricaoBusiness = InscricaoBusiness::getInstance($this->db);

        $parametros = $request->getQueryParams();

        $evento = is_array($parametros) && array_key_exists("evento", $parametros) ? filter_var($parametros["evento"], FILTER_SANITIZE_STRING) : NULL;

        unset($parametros["evento"]);

        $inscricoes = $inscricaoBusiness->listar($evento, $parametros);
        
        return $response->withJson($inscricoes);
    });
})->add($validacaoRenovacaoMiddleware)->add($jwtAuthenticationMiddleware);

/*
 * Recupera uma imagem de acordo com o parâmetro.
 */
$app->get('/eventos/imagens/{nome}', function ($request, $response, $args) {
    $response->write(file_get_contents(__DIR__ . "/../uploads/" . $args["nome"]));

    return $response->withHeader("Content-Type", FILEINFO_MIME_TYPE);
});

/*
 * Formulário de teste para login.
 */
$app->get('/login', function ($request, $response, $args) {
    return $this->renderer->render($response, 'login.phtml', $args);
});

/*
 * Hash de senha.
 */
$app->get('/hash', function ($request, $response, $args) {
   $response->getBody()->write(
           password_hash("123456", PASSWORD_BCRYPT, ["cost" => 12]));

   return $response;
});