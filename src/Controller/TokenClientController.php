<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TokenClientController extends AbstractController {

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['openid email'], []);
    }

    /**
     * Checking connection
     *
     * @Route("/connect/google/check", name="connect_google_check")
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     * @return Response
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        //Fetch client from Google
        $client = $clientRegistry->getClient('google');
        try {
            $accessToken = $client->getAccessToken()->getValues();
            if (key_exists('id_token', $accessToken)) {
                $accessTokenId = $accessToken['id_token'];
                return new JsonResponse(['id_token' => $accessTokenId, 'status' => true, 'message' => 'Token!']);
            }
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
            die;
        }
    }

}
