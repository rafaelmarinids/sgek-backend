<?php

namespace Util;

use \Firebase\JWT\JWT;
use \Util\Comum;
use \Util\Encryption;

/**
 * Classe auxiliar que fornece operações que geram e/ou utilizam token.
 *
 * @author rafael
 */
class TokenHelper {
    
    /**
     * 
     * @param type $usuario
     * @return type
     */
    public static function gerarToken($usuario) {
        $agora = time();
            
        $payload = [
            'iat'  => $agora,                                                   // Issued at: time when the token was generated
            'jti'  => base64_encode(mcrypt_create_iv(32)),                      // Json Token Id: an unique identifier for the token
            'iss'  => "SGEK",                                                   // Issuer: pode ser o domínio posteriormente
            //'nbf'  => $agora + 10,                                              // Not before: daqui à 10 segundos
            'exp'  => $agora + (60 * 60),                                       // Expire: daqui à 1 hora
            'data' => self::criptografarUsuario($usuario)                       // Data related to the signer user
        ];
        
        return JWT::encode($payload,                                            // Data to be encoded in the JWT
            Comum::$PALAVRA_SECRETA,                                            // The signing key
            Comum::$ALGORITMO_CRIPTOGRAFIA_TOKEN);                              // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
    }
    
    /**
     * 
     * @param type $token
     */
    public static function renovarToken($token) {        
        return self::gerarToken(self::recuperarUsuario($token));
    }
    
    /**
     * 
     * @param type $token
     */
    public static function isValido($token) {
        $payload = self::recuperarPayload($token);
        
        $agora = time();
        
        if ($agora > $payload["exp"]) {
            return FALSE;
        }
        
        return TRUE;
    }
    
    /**
     * 
     * @param type $token
     */
    public static function recuperarPayload($token) {
        JWT::$leeway = 60;                                                      // $leeway in seconds
        
        return (array) JWT::decode($token, 
            Comum::$PALAVRA_SECRETA, 
            array(Comum::$ALGORITMO_CRIPTOGRAFIA_TOKEN));
    }
    
    /**
     * 
     * @param type $token
     */
    public static function recuperarUsuario($token) {
        JWT::$leeway = 60;                                                      // $leeway in seconds
        
        $decodedToken = (array) JWT::decode($token, 
            Comum::$PALAVRA_SECRETA, 
            array(Comum::$ALGORITMO_CRIPTOGRAFIA_TOKEN));
        
        $usuario = self::descriptografarUsuario($decodedToken["data"]);
        
        return $usuario;
    }
    
    /**
     * 
     * @param type $usuario
     * @return type
     */
    public static function criptografarUsuario($usuario) {
        /*$encryption = Encryption::getInstance(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        
        return $encryption->encrypt(json_encode($usuario), Comum::$PALAVRA_SECRETA);*/
        
        return json_encode($usuario);
    }
    
    /**
     * 
     * @param type $usuarioCriptografado
     * @return type
     */
    public static function descriptografarUsuario($usuarioCriptografado) {
        /*$encryption = Encryption::getInstance(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        
        return json_decode($encryption->decrypt($usuarioCriptografado, Comum::$PALAVRA_SECRETA));*/
        
        return json_decode($usuarioCriptografado);
    }
    
}
