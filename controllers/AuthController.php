<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;

class AuthController extends Controller
{  
    public function behaviors() 
    {   
        $behaviors = parent::behaviors();
     
    	$behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'login',
                'options',
            ]
        ];

		return $behaviors;
	}

    private function generateJwt($id) 
    {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();    
        
        $now   = new \DateTimeImmutable();
        
        $jwtParams = Yii::$app->params['jwt'];
    
        $token = $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify($jwtParams['request_time']))
            ->expiresAt($now->modify($jwtParams['expire']))
            ->withClaim('uid', $id)
            ->getToken($signer, $key);
        
        return [

            "token"=> $token->toString()
        ];
        
    }
    
    public function actionLogin() 
    {
		$model = json_decode(file_get_contents('php://input'), true);

		if ($model) 
        {            
            $user = new \app\models\Users();

            $id = $user->findByUsername($model["username"], $model["password"]);     

            if($id > 0)
            {
                $token = $this->generateJwt($id);
                return $token;
            }			
		} 

        throw new \yii\web\UnauthorizedHttpException;
	}    
}