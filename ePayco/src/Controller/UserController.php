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

class UserController extends AbstractController
{

    public function index(): Response
    {
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $users = $user_repo->findAll();
        // $data = [
        //     ''
        // ];
        return $this->resjson($users);
    }

    public function create(Request $request){
        //Recoger los datos por post y decodificar el json
        $params = json_decode($request->getContent(), true);
        //respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'proceso invalido!'
        ];
        //comprobar y validar datos
        if(!empty($params))
        {
            $name = $params['name'];
            $tipe_doc = $params['tipe_doc'];
            $number_doc = $params['number_doc'];
            $public_key = $params['public_key'];
            $private_key = $params['private_key'];
            $email = $params['email'];
            $password = $params['password'];
            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);
            //validar si no hay algun error
            $validate_email_result = count($validate_email);
            if(!empty($email) && 
               $validate_email_result == 0 &&
               !empty($password) &&
               !empty($name) &&
               !empty($tipe_doc) &&
               !empty($number_doc) &&
               !empty($public_key) &&
               !empty($private_key)
               )
            {
            //si la validacioón es correcta, crear el objeto del usuario
                $user = new User();
                $user->setName($name);
                $user->setTipeDoc($tipe_doc);
                $user->setNumberDoc($number_doc);
                $user->setPublicKey($public_key);
                $user->setPrivateKey($private_key);
                $user->setEmail($email);
                $user->setCreatedAt(new \Datetime('now'));
            //cifrar la contraseña
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

            //comprobar si el usuario existe (duplicado)
             /*entitymanager(doctrine) capa de astraccion para acceder, realizar consultas o procesar informacion en base a los modelos*/
                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();

                $user_repo = $doctrine->getRepository(User::class);
                $isset_user = $user_repo->findBy(array(
                    'email' => $email
                ));
            //si no existe, guardarlo en BD
                if(count($isset_user) == 0)
                {
                //guardo el usuario
                //persistir el objeto usuario en el entitymanager(doctrine)
                $em->persist($user);
                //guardar directamente a la BD
                $em->flush();
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'proceso realizado exitosamente!'
                ];
                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'proceso invalido!'
                    ];
                }
            }
        }
        //hacer respuesta en json
        return $this->resjson($data);
    }

    public function login(Request $request, JwtAuth $jwt_auth)
    {
        // Recibir los datos por post
        $params = json_decode($request->getContent(), true);
        // array por defecto para devolver
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'proceso invalido!'
        ];
        // comprobar y validar datos
        if(!empty($params))
        {
            $email = (!empty($params['email']) ? $params['email'] : null );
            $password = (!empty($params['password']) ? $params['password'] : null );
            $gettoken = (!empty($params['gettoken']) ? $params['gettoken'] : null );
            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);
            //validar si no hay algun error
            $validate_email_result = count($validate_email);
            if(!empty($email) && 
                $validate_email_result == 0 &&
                !empty($password)
                )
            {
                //Cigrar la contraseña
                $pwd = hash('sha256',$password);
                //si todo es ok crear token JWT de session de login
                if($gettoken)
                {   
                    $signup = $jwt_auth->signup($email, $pwd, $gettoken);
                }else{
                    $signup = $jwt_auth->signup($email, $pwd);
                }
                return new JsonResponse($signup);
            }
        }
        //hacer respuesta en json
        return $this->resjson($data);
    }

    
    public function edit(Request $request, JwtAuth $jwt_auth)
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
        //si es correcto, hacer la actualizacion del usuario
        if($authCheck)
        {
            //actualizar al usuario
            //conseguir entity manager
            $em = $this->getDoctrine()->getManager();
            //conseguir los datos del usuario identificado
            $identity = $jwt_auth->checkToken($token, true);
            //conseguir el usuario a actualizar
            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $user = $user_repo->findOneBy([
                'id' => $identity->id
            ]);
            //recoger datos por post
            $params = json_decode($request->getContent(), true);
            //comprobar y validar los datos
            if(!empty($params))
            {
                $tipe_doc = $params['tipe_doc'];
                $number_doc = $params['number_doc'];
                $email = $params['email'];
                $public_key = $params['public_key'];
                $private_key = $params['private_key'];
                $validator = Validation::createValidator();
                $validate_email = $validator->validate($email, [
                    new Email()
                ]);
                //validar si no hay algun error
                $validate_email_result = count($validate_email);
                if($validate_email_result == 0 &&
                   !empty($tipe_doc) &&
                   !empty($number_doc)
                   )
                {
                    //asignar nuevos datos al objeto del usuario
                    $user->setTipeDoc($tipe_doc);
                    $user->setNumberDoc($number_doc);
                    $user->setPublicKey($public_key);
                    $user->setPrivateKey($private_key);
                    //comprobar duplicados
                    $isset_user = $user_repo->findBy(array(
                        'email' => $email
                    ));
                //si no existe, guardarlo en BD
                    if(count($isset_user) == 0 || $identity->email == $email)
                    {
                    //persistir el objeto usuario en el entitymanager(doctrine)
                    $em->persist($user);
                    //guardar directamente a la BD
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'proceso realizado exitosamente!'
                    ];
                    }else{
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'proceso invalido!'
                        ];
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
