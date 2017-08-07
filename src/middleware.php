<?php

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

use \Util\Comum;
use \Util\ValidacaoRenovacaoMiddleware;

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
$validacaoRenovacaoMiddleware = new ValidacaoRenovacaoMiddleware($container['db']);