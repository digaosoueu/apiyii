<?php

namespace app\models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

use Yii;

class Auth
{  
    public function checkToken()
    {
        $response = false;

        $jwt = Yii::$app->jwt;
        $key = $jwt->key;
        
        $token = $this->getBearerToken();
        
        if($token)
        { 
            try
            {
                $valid = JWT::decode($token, new Key($key, 'HS256'));
                if($valid)
                {
                    $response = true;
                }
            }
            catch (ExpiredException $e) {
                throw new \yii\web\UnauthorizedHttpException;
            }
                        
        }
        
        return $response;
    }

    public function getBearerToken() 
    {
        $headers = $this->getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) 
        { 
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } 
        elseif (function_exists('apache_request_headers')) 
        {
            $requestHeaders = apache_request_headers();              
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            
            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}