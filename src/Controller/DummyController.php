<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp\Client;

class DummyController extends AbstractController
{
    #[Route('/dummy', name: 'app_dummy')]
    public function index(): Response
    {

        $auth_config = [
            'client_id' => '798198146189-hau8p72cpk0vgu3pq9h3p6rea5u8cajm.apps.googleusercontent.com',
            'client_secret' => '',
            'grant_type' => 'authorization_code',
            //'redirect_uri' => 'https://localhost:8080/connect',
            'scope' => 'openid email'

        ];

        $httpClient = new Client();
        $request = $httpClient->post('https://oauth2.googleapis.com/token', [ 'body' => json_encode($auth_config) ]);

        var_dump($request);die();

        // make a request to the token url
        $response = $request->send();
        $responseBody = $response->getBody(true);
        //var_dump($responseBody);die;


        return $this->render('dummy/index.html.twig', [
            'controller_name' => 'DummyController',
            'response_http' => $responseBody
        ]);
    }
}
