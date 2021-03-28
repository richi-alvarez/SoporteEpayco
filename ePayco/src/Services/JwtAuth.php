<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth{

    public $manager;

    public function __construct($manager){
        $this->manager = $manager;
        $this->key = 'secret_key_12345$$';
    }

    public function signup($email, $password, $gettoken = null){
        //comprobar si el usuario existe
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $password
        ]);
        $signup = false;
        //si existe, generar el token
        if(is_object($user))
        {
            $signup = true;
        }
        if($signup)
        {
            $token = [
                'id'=>$user->getId(),
                'name'=>$user->getName(),
                'email'=>$user->getEmail(),
                'iat'=>time(),//tiempo de creacion del token
                'exp'=>time()+(900),  //tiempo de expiracion (en segundos)
            ];

            //generar token
            $jwt = JWT::encode($token, $this->key, 'HS256');
            if(!empty($gettoken))
            {
                $data = $jwt;
            }else{
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decoded;
            }

        }else{
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto'
            ];
        }
        //comprbar el token y realizar condicional
        return $data;
    }

    public function checkToken($jwt, $identity = false){
		$auth = false;
			try{
				$decoded = JWT::decode($jwt,$this->key,['HS256']);
				}catch(\UnexpectedValueException $e){
					$auth = false;
				}catch(\DomainException $e){
					$auth = false;
				}
	
			if (isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->id) ) {
					$auth = true;
				}else{
					$auth = false;
			}

			if ($identity != false) {
				return $decoded;
			}else{
				return $auth;
			}
        }

}

?>