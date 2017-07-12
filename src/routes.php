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