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
        
        return $response->withJson($eventoBusiness->listar());
    });

    /*
     * Recupera um evento por id.
     */
    $this->get('/eventos/{id}', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);
        
        return $response->withJson($eventoBusiness->recuperar((int) $args["id"]));
    });

    /*
     * Salva um evento.
     */
    $this->post('/eventos', function ($request, $response, $args) {
        $eventoBusiness = EventoBusiness::getInstance($this->db);

        $parametros = $request->getParsedBody();

        $arquivos = $request->getUploadedFiles();

        try {
            $evento = $eventoBusiness->salvar(filter_var($parametros['titulo'], FILTER_SANITIZE_STRING),
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
})->add($validacaoRenovacaoMiddleware)->add($jwtAuthenticationMiddleware);

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