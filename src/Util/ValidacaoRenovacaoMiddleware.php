<?php

namespace Util;

use \Util\Comum;
use \Util\TokenHelper;
use \Business\LogBusiness;

/**
 * 
 */
class ValidacaoRenovacaoMiddleware {

    private $db;

    /**
     * Constructor!
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        $server = $request->getServerParams();

        if ($request->getUri()->getPath() != "/rs/autenticacao") {
            $token = str_replace("Bearer ", "", $request->getHeader("Authorization")[0]);
        
            /*
             * Antes da execução do serviço.
             * 
             * Verifica a validade do token.
             */
            if (!TokenHelper::isValido($token)) {
                /*
                 * Registra um acesso inválido.
                 */ 
                LogBusiness::getInstance($this->db)->inserir(
                    TokenHelper::recuperarUsuario($token),
                    $request->getMethod(), 
                    $request->getUri()->getPath(),
                    http_build_query($request->getQueryParams()),
                    $server["REMOTE_ADDR"],
                    $server["HTTP_USER_AGENT"],
                    "Tentativa de acesso inválido. (Token: $token)"
                );

                return $response->withStatus(401) // 401 Unauthorized
                    ->withHeader("Content-Type", "text/plain")
                    ->write("Não é possível atender a solicitação, a sessão expirou, por favor identifique-se novamente!");
            } else {
                /*
                 * Registra um acesso válido.
                 */ 
                LogBusiness::getInstance($this->db)->inserir(
                    TokenHelper::recuperarUsuario($token)->nome,
                    $request->getMethod(), 
                    $request->getUri()->getPath(),
                    http_build_query($request->getQueryParams()),
                    $server["REMOTE_ADDR"],
                    $server["HTTP_USER_AGENT"],
                    ""
                ); 
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
            /*
             * Registra o acesso durante a autenticação.
             */ 
            LogBusiness::getInstance($this->db)->inserir(
                "",
                $request->getMethod(), 
                $request->getUri()->getPath(),
                http_build_query($request->getQueryParams()),
                $server["REMOTE_ADDR"],
                $server["HTTP_USER_AGENT"],
                ""
            );

            return $next($request, $response);
        }
    }
    
}
