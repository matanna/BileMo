<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * This class is use for send a GET request to GoogleAccount API for check Bearer token
 */
class ClientForGoogle
{
    private $clientForGoogle;

    public function __construct(HttpClientInterface $clientForGoogle)
    {
        $this->clientForGoogle = $clientForGoogle;
    }

    public function getUserInformations($accessToken): array
    {
        $response = $this->clientForGoogle->request(
            'GET',
            'https://openidconnect.googleapis.com/v1/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer' . $accessToken
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        if ($statusCode != 200) {
            throw new AuthenticationException("Le token est invalide ou est expiré. Vous ne pouvez pas avoir accès aux ressources demandées.", $statusCode);
        }

        $contentType = $response->getHeaders()['content-type'][0];
        
        // $contentType = 'application/json'

        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'

        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }
}
