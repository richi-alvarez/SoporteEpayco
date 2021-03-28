<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Entity\User;
use App\Services\JwtAuth;

class InvoiceController extends AbstractController
{
    public $url;

    public function __construct(){
        $this->url = 'https://apify.epayco.co';
    }
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InvoiceController.php',
        ]);
    }

    public function getInvoice ( Request $request, JwtAuth $jwt_auth)
    {
        //recojer la cabecera de autentificación
        $token = $request->headers->get('Authorization');
        //crear un metodo para comprobar si el token es correcto
        $authCheck = $jwt_auth->checkToken($token);
        // array por defecto para devolver
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'sesión expirada'
        ];
        if($authCheck)
        {
            //recoger datos por post
            $params = json_decode($request->getContent(), true);
            //conseguir los datos del usuario identificado
            $identity = $jwt_auth->checkToken($token, true);
             //comprobar y validar los datos
             if(!empty($params))
             {
                 $tipe_doc = $params['tipe_doc'];
                 $number_doc = $params['number_doc'];
                 //validar si no hay algun error
                 if(
                    !empty($tipe_doc) &&
                    !empty($number_doc)
                    )
                 { 
                    //traer la informacion del usuario
                       $em = $this->getDoctrine()->getManager(); 
                       $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                           'id'=>$identity->id
                       ]);
                       if($user){
                           //validar que los datos enviados de consulta correspondan con los datos del usuario registrado
                           if($user->getTipeDoc() == $tipe_doc &&
                            $user->getNumberDoc() == $number_doc)
                           {
                            // $json = '{
                            //     "projectId":356,
                            //     "document":"71698318"
                            // }';
                            // try {
                            //     $curl = curl_init();
                            //     curl_setopt_array($curl, array(
                            //     CURLOPT_URL =>$this->url. "/billcollect/invoices/consult",
                            //     CURLOPT_RETURNTRANSFER => true,
                            //     CURLOPT_ENCODING => "",
                            //     CURLOPT_MAXREDIRS => 10,
                            //     CURLOPT_TIMEOUT => 0,
                            //     CURLOPT_FOLLOWLOCATION => true,
                            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            //     CURLOPT_CUSTOMREQUEST => "POST",
                            //     CURLOPT_POSTFIELDS =>$json,
                            //     CURLOPT_HTTPHEADER => array(
                            //         "Content-Type: application/json",
                            //         "Accept: application/json"
                            //     ),
                            //     ));
                            //     $result = curl_exec($curl);
                            //     curl_close($curl);
                            //     $data = json_decode($result, true);
                            // } catch (\Throwable $th) {
                            //     throw $th;
                            // }
                          
                                $data = [
                                    'status' => 'success',
                                    'code' => 200,
                                    'message' => 'proceso realizado exitosamente!'
                                ];
                           }else{
                                $data = [
                                    'status' => 'error',
                                    'code' => 400,
                                    'message' => 'los datos no concuerdan!'
                                ];
                           }
                           
                       }
                    
                 }
        }
    }
      //hacer respuesta en json
      return $this->resjson($data);
}
    private function resjson($data){
        //Serializar datos con servicio serializer(objeto->texto)
        $json = $this->get('serializer')->serialize($data, 'json');
        //response con httpfoundation (crear objeto de respuesta)
        $response = new Response();
        //Asignar contenido a la respuesta
        $response->setContent($json);
        //Indicar formato de respuesta
        $response->headers->set('Content-Type', 'application/json');
        //Devolver la respuesta
        return $response;
    }
}
