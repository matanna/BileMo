<?php

namespace App\Security;

use App\Entity\Client;
use App\Client\ClientForGoogle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class GoogleAuthenticator extends AbstractGuardAuthenticator
{
    private $entityManager;
    private $clientForGoogle;

    public function __construct(EntityManagerInterface $entityManager, ClientForGoogle $clientForGoogle) 
    {
        $this->entityManager = $entityManager;
        $this->clientForGoogle = $clientForGoogle;
    }
 
    /**
     * Method supports
     * Verify if Authorization header exist 
     *
     * @param Request $request 
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }
   
    /**
     * Method getCredentials
     * Retrieves Bearer token in Authorization header
     *
     * @param Request $request 
     *
     * @return string
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('Authorization');
    }
    
    /**
     * Method getUser
     * This method check if the token is valid in Google
     *
     * @param $credentials
     * @param UserProviderInterface $clientProvider 
     *
     * @return UserInterface $client
     */
    public function getUser($credentials, UserProviderInterface $clientProvider)
    {
        $credentials = substr($credentials, 7);
   
        //Check Bearer token with ClientForGoogle class
        $data = $this->clientForGoogle->getUserInformations($credentials);

        $client = $clientProvider->loadUserByUsername($data['sub']);
        
        //if client dont exist, we create this and persist in database
        if ($client == []) {
            $client = new Client();
            $client->setGoogleId($data['sub'])
                   ->setUsername($data['name'])
                   ->setFullName($data['given_name'] . ' ' . $data['family_name'])
                   ->setEmail($data['email'])
                   ->setLocale($data['locale'])
            ; 
            $this->entityManager->persist($client);
            $this->entityManager->flush(); 
        }
        return $client;
        
    }

    //This is not useful for an API
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'status' => 401 . ': Unauthorized',
            'message' => $exception->getMessage(),
        ], 401);
    }


    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse([
            'status' => 401 . ': Unauthorized',
            'message' => 'Vous devez vous identifier avec un Bearer token dans votre requête',
        ], 401);
    }

    //This is not useful for an API
    public function supportsRememberMe()
    {
        return false;
    }
}
