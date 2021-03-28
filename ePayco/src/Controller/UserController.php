<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Entity\User;

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
            'code' => 200,
            'message' => 'El usuario no se ha creado.'
        ];
        //comprobar y validar datos
        if(!empty($params))
        {
            $name = $params['name'];
            $tipe_doc = $params['tipe_doc'];
            $number_doc = $params['number_doc'];
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
               !empty($name)
               )
            {
            //si la validacioón es correcta, crear el objeto del usuario
                $user = new User();
                $user->setName($name);
                $user->setTipeDoc($tipe_doc);
                $user->setNumberDoc($number_doc);
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
                        'message' => 'Usuario creado correctamente',
                        'user' => $user
                ];
                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El usuario ya existe.'
                    ];
                }
            }
        }
            //hacer respuesta en json
        return $this->resjson($data);
    }

    public function login(Request $request)
    {
        // Recibir los datos por post

        // array por defecto para devolver

        // comprobar y 

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
