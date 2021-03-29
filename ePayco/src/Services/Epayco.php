<?php
namespace App\Services;

use App\Entity\User;

class Epayco{

    public $manager;
    public $url;

    public function __construct($manager)
    {   
        $this->manager = $manager;
        $this->url = 'https://api.secure.payco.co';
    }

    public function requestJwt($email){
        //comprobar si el usuario existe
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);
        $jwtToken = false;
        //si existe, generar el token
        if(is_object($user))
        {
            $jwtToken = true;
        }
        if($jwtToken)
        {
            $auth = base64_encode($user->getPublicKey() . ":" . $user->getPrivateKey());
           $data_ = array(
                'public_key' => $user->getPublicKey(),
                'private_key' => $user->getPrivateKey()
            );
            $header = array(
                'type: sdk-jwt',
                'Authorization: Basic $auth',
                'Content-Type: application/json'
            );
    
            $json=  json_encode($data_);
    
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $this->url.'/v1/auth/login',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$json,
              CURLOPT_HTTPHEADER => $header,
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, true);
        }else{
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto'
            ];
        }
        return $data;
    }
   
}

?>