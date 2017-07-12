<?php

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

use \Util\Comum;
use \Util\TokenHelper;

/*
 * Adiciona o middleware que valida a autenticidade o token.
 */
$jwtAuthenticationMiddleware = new \Slim\Middleware\JwtAuthentication([
    "secure" => false,
    "path" => ["/rs"],
    "passthrough" => ["/rs/autenticacao"],
    "secret" => Comum::$PALAVRA_SECRETA
]);

/*
 * Adiciona a clousure que valida e renova o token após as rotas serem 
 * resolvidas.
 */
$validacaoRenovacaoMiddleware = function ($request, $response, $next) {
    if ($request->getUri()->getPath() != "/rs/autenticacao") {
        $token = str_replace("Bearer ", "", $request->getHeader("Authorization")[0]);
    
        /*
         * Antes da execução do serviço.
         * 
         * Verifica a validade do token.
         */
        if (!TokenHelper::isValido($token)) {
            return $response->withStatus(401) // 401 Unauthorized
                ->withHeader("Content-Type", "text/plain")
                ->write("Não é possível atender a solicitação, a sessão expirou, por favor identifique-se novamente!");
        }

        $response = $next($request, $response);

        /*
         * Depois da execução do serviço.
         * 
         * Renova a validade do token.
         */    
        if ($token) {
            $token = TokenHelper::renovarToken($token);

            $newResponse = $response->withHeader("Authorization", "Bearer " . $token);
            
            return $newResponse;
        }
        
        return $response;
    } else {
        return $next($request, $response);
    }   
};