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
            'message' => 'El usuario no se ha creado.',
            'json' => $params
        ];
        //comprobar y validar datos
        if(!empty($params))
        {
            $name = $params['name'];
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
                $data = [
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'VALIDACION CORRECTA'
                ];
            }else{
                $data = [
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'VALIDACION IN-CORRECTA'
                ];
            }
        }
        //si la validacioón es correcta, crear el objeto del usuario

        //cifrar la contraseña

        //comprobar si el usuario existe (duplicado)

        //si no existe, guardarlo en BD

        //hacer respuesta en json
        return new JsonResponse($data);
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
