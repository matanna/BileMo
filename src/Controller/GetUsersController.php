<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GetUsersController extends AbstractController
{
    /**
     * @Route("/client/{name}/users", name="list_users")
     * 
     * @ParamConverter("client", options={"mapping": {"name" = "name"}})
     */
    public function getUsers(
        UserRepository $userRepository, Request $request, Client $client = null
    ): Response {

        $users = $userRepository->findUsersByClient($client, $request);

        return $this->json($users, 200, [], [
            'groups' => 'list_users'
        ]);

    }
}
