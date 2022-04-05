<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Client;

class IrisClientController extends AbstractController {

    /**
     * Call to get Projects webservice POST method
     *
     * @Route("/irisapi/getprojects", name="iris_get_projects")
     */
    public function getProjectsAction(Request $request): Response
    {
        $sessionIrisToken =  $request->getSession()->get('iris_token');
        if ($sessionIrisToken) {
            $irisEndpoint = $this->getParameter('app.irisendpoint');

            $guzzleClient = new Client(['base_uri' => $irisEndpoint]);

            $rawResponse = $guzzleClient->post('/projects', [
                'headers' => ['Authorization' => 'Bearer ' . $sessionIrisToken],
                //TODO construir el body con los params adecuados
                //'body' => json_encode($data),
            ]);

            $response = $rawResponse->getBody()->getContents();

        } else {
            $response = "No token";
        }


        return $this->render('dummy/index.html.twig', [
            'called_method' => 'getProjects',
            'response_http' => $response
        ]);
}

    /**
     * Call to get Profiles webservice POST method
     *
     *
     * @Route("/irisapi/getprofiles", name="iris_get_profiles")
     */
    public function getProfilesAction(Request $request): Response
    {
        $sessionIrisToken =  $request->getSession()->get('iris_token');
        if ($sessionIrisToken) {
            $irisEndpoint = $this->getParameter('app.irisendpoint');

            $guzzleClient = new Client(['base_uri' => $irisEndpoint]);

            $rawResponse = $guzzleClient->post('/profiles', [
                'headers' => ['Authorization' => 'Bearer ' . $sessionIrisToken],
            ]);

            $response = $rawResponse->getBody()->getContents();

        } else {
            $response = "No token";
        }


        return $this->render('dummy/index.html.twig', [
            'called_method' => 'getProfiles',
            'response_http' => $response
        ]);
    }

}
