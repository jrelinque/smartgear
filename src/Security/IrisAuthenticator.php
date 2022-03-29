<?php

namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class IrisAuthenticator extends OAuth2Authenticator {
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ? bool {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport {
        //Creating the connection client with Google
        $client = $this->clientRegistry->getClient('google');
        $id_token = "";
        $accessToken = $this->fetchAccessToken($client);

        //Let's try to catch the access token. Also, we need the id_token value
        if ($accessToken && $accessToken->getValues()) {
            $values = $accessToken->getValues();
            if (key_exists('id_token', $values)) {
                $id_token = $values['id_token'];
            }
        }

        //We're going to put token on session
        $request->getSession()->set('token', $id_token);

        return new SelfValidatingPassport(
        new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
            $irisUser = $client->fetchUserFromToken($accessToken);
            $email = $irisUser->getEmail();

        // 1) have they logged in with Google / IRIS before? Easy!
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['irisId' => $irisUser->getId()]);

        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setIrisId($irisUser->getId());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
         })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        $targetUrl = $this->router->generate('app_home');

        return new RedirectResponse($targetUrl);

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
